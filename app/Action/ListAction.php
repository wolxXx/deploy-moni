<?php

namespace Application\Action;

class ListAction extends AbstractAction
{
    public function run(): \Psr\Http\Message\ResponseInterface
    {
        $data = [];
        foreach ($this->pdo->query('SELECT DISTINCT group_name, created_at, name FROM deployments ORDER BY created_at DESC') as $row) {
            if (false === \array_key_exists($row['group_name'], $data)) {
                $data[$row['group_name']] = [];
            }
            $data[$row['group_name']][] = [
                'created_at' => $row['created_at'],
                'name'       => $row['name'],
            ];
        }

        return \Application\JsonGateway::Factory(
            response: $this->response,
            data    : $data,
        );
    }
}