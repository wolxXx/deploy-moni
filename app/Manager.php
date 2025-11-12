<?php

namespace Application;

class Manager
{
    public static function getLogger(): \Monolog\Logger
    {
        $logger = new \Monolog\Logger(name: 'deploy_monitor');
        $logger->pushHandler(handler: new \Monolog\Handler\StreamHandler(stream: 'php://stdout', level: \Monolog\Level::Debug));
        $logger->pushHandler(handler: new \Monolog\Handler\StreamHandler(stream: 'php://stderr', level: \Monolog\Level::Error));
        $logger->pushHandler(handler: new \Monolog\Handler\StreamHandler(stream: __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'log', level: \Monolog\Level::Debug));

        return $logger;
    }

    public static function Factory(): void
    {
        $app = \Slim\Factory\AppFactory::create();
        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(displayErrorDetails: true, logErrors: true, logErrorDetails: true, logger: self::getLogger());

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
            $pdo = new \PDO(dsn: $dsn, username: $user, password: $pass, options: $opt);
        } catch (\PDOException $e) {
            throw new \PDOException(message: $e->getMessage(), code: (int)$e->getCode());
        }

        $app->get(pattern: '/random', callable: function (\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($pdo) {
            try {
                return new \Application\Action\RandomAction(response: $response, request: $request, pdo: $pdo)->run();
            } catch (\Throwable $e) {
                \Application\Manager::getLogger()->error($e);
                return $response->withStatus(code: 500);
            }
        });

        $app->get(pattern: '/', callable: function (\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($pdo) {
            try {
                return new \Application\Action\IndexAction(response: $response, request: $request, pdo: $pdo)->run();
            } catch (\Throwable $e) {
                \Application\Manager::getLogger()->error($e);
                return $response->withStatus(code: 500);
            }
        });

        $app->get(pattern: '/recent', callable: function (\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($pdo) {
            try {
                return new \Application\Action\RecentAction(response: $response, request: $request, pdo: $pdo)->run();
            } catch (\Throwable $e) {
                \Application\Manager::getLogger()->error($e);
                return $response->withStatus(code: 500);
            }
        });

        $app->get(pattern: '/api/v1/log', callable: function (\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($pdo) {
            try {
                return new \Application\Action\LogAction(response: $response, request: $request, pdo: $pdo)->run();
            } catch (\Throwable $e) {
                \Application\Manager::getLogger()->error($e);
                return $response->withStatus(code: 500);
            }
        });

        $app->get(pattern: '/api/v1/items', callable: function (\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($pdo) {
            try {
                return new \Application\Action\ListAction(response: $response, request: $request, pdo: $pdo)->run();
            } catch (\Throwable $e) {
                \Application\Manager::getLogger()->error($e);
                return $response->withStatus(code: 500);
            }
        });

        $app->run();
    }
}
