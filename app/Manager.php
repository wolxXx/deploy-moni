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
            $groupNames = $pdo->query('select distinct group_name from deployments order by group_name ASC')->fetchAll();
            foreach ($groupNames as $groupName) {
                $groups[$groupName['group_name']] = [];
                $data                             = $pdo->query('select * from deployments where group_name = "' . $groupName['group_name'] . '" order by created_at DESC limit 3')->fetchAll();
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
<html lang="en">
<head>
    <link href="/styles.css?t={$random}" media="screen" rel="stylesheet" type="text/css"/>    
    <title>Deployment Monitor</title>
</head>
<body>
<div id="container">
<h1>Deployment Monitor</h1>
<table>
<thead>
    <tr>
        <th>Group</th>
        <th colspan="2">Deployments</th>
    </tr>
</thead>
<tbody>

HTML;
            foreach ($groups as $groupName => $data) {
                $html .= <<<HTML
<tr>
<td>{$groupName}</td>
<td>

HTML;
                foreach ($data as $deployment) {
                    $html .= $deployment['created_at'] . ': ' . $deployment['name'] . '<br>';
                }
                $html .= <<<HTML

</td>
</tr>

HTML;
            }
            $html .= <<<HTML

</tbody>
</table>
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
            if(false === $success) {
                throw new \PDOException("query failed", 500);
            }

            return $response->withStatus(204);
        });

        $app->run();
    }
}
