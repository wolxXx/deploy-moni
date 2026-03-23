<?php

declare(strict_types=1);

namespace Application\Action;

class DeleteGroupAction extends AbstractAction
{
    public function run(): \Psr\Http\Message\ResponseInterface
    {
        $prepare = $this
            ->pdo
            ->prepare(query: 'delete from deployments where group_name = ?')
        ;
        $prepare->execute(params: [\base64_decode($this->arguments['id'])]);

        return $this->response->withStatus(code: 204);
    }
}
