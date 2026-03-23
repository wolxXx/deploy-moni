<?php

declare(strict_types=1);

namespace Application\Action;

class DeleteItemAction extends AbstractAction
{
    public function run(): \Psr\Http\Message\ResponseInterface
    {
        $prepare = $this
            ->pdo
            ->prepare(query: 'delete from deployments where id = ?')
        ;
        $prepare->execute(params: [$this->arguments['id']]);

        return $this->response->withStatus(code: 204);
    }
}
