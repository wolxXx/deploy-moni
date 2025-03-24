<?php

namespace Application;

class Manager
{
    public static function Factory()
    {
        $app = \Slim\Factory\AppFactory::create();
        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(true, true, true);

        $host    = 'db';
        $db      = 'deploy_monitor';
        $user    = 'root';
        $pass    = 'root';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new \PDO($dsn, $user, $pass, $opt);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

        $app->get('/random', function (\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($pdo) {
            $pdo->query('delete from deployments');
            foreach (range(0, 10) as $groupCounter) {
                $begin = new \DateTime('2024-01-01 01:00:00');
                foreach (\range(0, rand(1, 100)) as $deploymentCounter) {
                    $pdo->query('insert into deployments(name, group_name, created_at) values ("' . $deploymentCounter . '", "' . $groupCounter . '", "' . $begin->format('Y-m-d H:i:s') . '")');
                    $begin->add(new \DateInterval('P1D'));
                }
            }

            return $response
                ->withHeader('Location', '/')
                ->withStatus(302)
            ;
        });

        $app->get('/', function (\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($pdo) {
            $groups     = [];
//            $groupNames = $pdo->query('select group_name from deployments x group by group_name order by max(x.created_at) DESC')->fetchAll();
            $groupNames = $pdo->query('select group_name from deployments x group by group_name order by group_name asc')->fetchAll();
            foreach ($groupNames as $groupName) {
                $groups[$groupName['group_name']] = [];
                $data                             = $pdo->query('select * from deployments where group_name = "' . $groupName['group_name'] . '" order by created_at DESC, id desc limit 2')->fetchAll();
                foreach ($data as $deployment) {
                    $groups[$groupName['group_name']][] = [
                        'id'         => $deployment['id'],
                        'name'       => $deployment['name'],
                        'created_at' => $deployment['created_at'],
                    ];
                }
            }
            $random = \rand(0, \PHP_INT_MAX);
            $html   = <<<HTML
<!DOCTYPE html>
<html lang="en" translate="no">
<head>
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/styles.css?t={$random}" media="screen" rel="stylesheet" type="text/css"/>    
    <title>Deployment Monitor</title>
    <script>
        window.setInterval(function () {
            const event = new Date();
            console.log(event.toLocaleTimeString('de-DE'));
            document.getElementById('clock').innerHTML = event.toLocaleDateString('de-DE') + ' '+  event.toLocaleTimeString('de-DE');            
        }, 150);
        window.setTimeout(function() {
            location.reload();          
        }, 20000);    
    </script>
</head>
<body>
<div id="container">
<!--<h1>Deployment Monitor</h1>-->
<!--<table>-->
<!--<thead>-->
<!--    <tr>-->
<!--        <th>Group</th>-->
<!--        <th colspan="2">Deployments</th>-->
<!--    </tr>-->
<!--</thead>-->
<div style="font-size: 1rem; display: flex; flex-wrap: wrap; width: 100%; gap: 5px; justify-content: left; overflow: hidden;">

HTML;
            foreach ($groups as $groupName => $data) {
                $html .= <<<HTML
<div style="display: flex; flex-direction: column; width: 15%; border: 1px solid white; padding: 0.2%; word-wrap: anywhere;">
<div style="text-align: left; font-weight: bold; font-size: 1.3em; padding-bottom: 2px;">{$groupName}</div>
<div>

HTML;
                $formatter = \IntlDateFormatter::create('de_DE', \IntlDateFormatter::RELATIVE_MEDIUM, \IntlDateFormatter::SHORT);
                foreach ($data as $idx => $deployment) {
                    $style = ' style="';
                    if ($idx !== count($data) - 1) {
                        $style .= ' border-bottom: 1px solid #cccccc60;';
                    }
                    if (0 === $idx) {
                        $style .= ' font-size: 1.1em; font-weight: bold; color: #fff;';
                    }
                    if (0 !== $idx) {
                        $style .= ' color: #ccc;';
                    }
                    $style .= ' word-wrap: anywhere;"';
                    $html .=  '<div' . $style . '><span style="color: #ccc; font-size: 0.79em; margin-right: 5px; display: inline-block;">' . $formatter->format(new \DateTime($deployment['created_at'])) . '</span> ' . $deployment['name'] . '</div>';
                }
                $html .= <<<HTML

</div>
</div>

HTML;
            }
            $html .= '<span id="clock" style="position: fixed; bottom: 0; right: 0; background: #222; padding-left: 10px; padding-top: 10px; border-top: solid 1px #666; border-left: solid 1px #666; border-radius: 4px 0px 0px 0px;"></span>';
            $html .= <<<HTML

</div>
<!--</table>-->
</div>
</body>
</html>
HTML;


            $response->getBody()->write($html);

            return $response;
        });

        $app->get('/api/v1/log', function (\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($pdo) {
            $header = $request->getHeader('x-api-key');
            if (0 === \sizeof($header)) {
                throw new \InvalidArgumentException("auth header missing", 400);
            }
            $apiKey  = $header[0];
            $prepare = $pdo->prepare('select * from deployment_key where value = ?');
            $prepare->execute([$apiKey]);
            $data = $prepare->fetchAll();
            if (0 == \sizeof($data)) {
                throw new \InvalidArgumentException("Invalid API key", 401);
            }

            $validKey = null;
            $now      = new \DateTime();
            foreach ($data as $key) {
                $isValid = true;
                if (null !== $key['valid_from']) {
                    $validFrom = new \DateTime($key['valid_from']);
                    if ($validFrom > $now) {
                        $isValid = false;
                    }
                }
                if (null !== $key['valid_until']) {
                    $validUntil = new \DateTime($key['valid_until']);
                    if ($validUntil < $now) {
                        $isValid = false;
                    }
                }
                if ($isValid) {
                    $validKey = $key;
                }
            }
            if (null === $validKey) {
                throw new \InvalidArgumentException("Invalid API key", 401);
            }

            parse_str($request->getUri()->getQuery(), $query);

            $group   = $query['group'] ?? null;
            $name    = $query['name'] ?? null;
            $created = $query['created'] ?? (new \DateTime())->format('Y-m-d H:i:s');
            if (null === $group || null === $name) {
                throw new \InvalidArgumentException("missing group or name", 400);
            }

            $prepared = $pdo->prepare('insert into deployments(name, group_name, created_at) values (:name, :group, :created)');
            $params   = [
                ':name'    => \base64_decode($name),
                ':group'   => \base64_decode($group),
                ':created' => $created,
            ];

            $success = $prepared->execute($params);
            if (false === $success) {
                throw new \PDOException("query failed", 500);
            }

            return $response->withStatus(204);
        });

        $app->run();
    }
}
