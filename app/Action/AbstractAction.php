<?php

declare(strict_types=1);

namespace Application\Action;

abstract class AbstractAction
{
    protected \Slim\Views\PhpRenderer $renderer;

    public final function __construct(protected \Psr\Http\Message\ResponseInterface $response, protected \Psr\Http\Message\RequestInterface $request, protected \PDO $pdo)
    {
        $this->renderer = new \Slim\Views\PhpRenderer(
            templatePath: \ROOT_DIR . DIRECTORY_SEPARATOR . 'app' . \DIRECTORY_SEPARATOR . 'views',
            layout      : 'layout' . \DIRECTORY_SEPARATOR . 'main.php'
        );
    }

    public abstract function run(): \Psr\Http\Message\ResponseInterface;

    protected function render(string $template, \Application\DataObject\View $view): \Psr\Http\Message\ResponseInterface
    {
        $view->dateFormatter = \IntlDateFormatter::create(locale: 'de_DE', dateType: \IntlDateFormatter::RELATIVE_MEDIUM, timeType: \IntlDateFormatter::SHORT);
        return $this
            ->renderer
            ->render(
                response: $this->response,
                template: $template,
                data    : [
                              'view' => $view
                          ]
            )
        ;
    }
}
