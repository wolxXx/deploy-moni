<?php

declare(strict_types = 1);

namespace Application\Action;

class DeleteItemAction extends AbstractAction
{
    public function run(): \Psr\Http\Message\ResponseInterface
    {
        $prepare = $this
            ->pdo
            ->prepare(query: 'DELETE FROM deployments WHERE id = ?')
        ;
        $prepare->execute(params: [$this->arguments['id']]);

        return $this->response->withStatus(code: 204);
    }
}
