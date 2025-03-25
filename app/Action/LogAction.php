<?php

declare(strict_types=1);

namespace Application\Action;

class LogAction extends AbstractAction
{
    public function run(): \Psr\Http\Message\ResponseInterface
    {
        $header = $this->request->getHeader(name: 'x-api-key');
        if (0 === count(value: $header)) {
            throw new \InvalidArgumentException(message: "auth header missing", code: 400);
        }
        $apiKey  = $header[0];
        $prepare = $this
            ->pdo
            ->prepare(query: 'select * from deployment_key where value = ?')
        ;
        $prepare->execute(params: [$apiKey]);
        $data = $prepare->fetchAll();
        if (0 == count(value: $data)) {
            throw new \InvalidArgumentException(message: "Invalid API key", code: 401);
        }

        $validKey = null;
        $now      = new \DateTime();
        foreach ($data as $key) {
            $isValid = true;
            if (null !== $key['valid_from']) {
                $validFrom = new \DateTime(datetime: $key['valid_from']);
                if ($validFrom > $now) {
                    $isValid = false;
                }
            }
            if (null !== $key['valid_until']) {
                $validUntil = new \DateTime(datetime: $key['valid_until']);
                if ($validUntil < $now) {
                    $isValid = false;
                }
            }
            if (true === $isValid) {
                $validKey = $key;
            }
        }
        if (null === $validKey) {
            throw new \InvalidArgumentException(message: "Invalid API key", code: 401);
        }

        parse_str(string: $this->request->getUri()->getQuery(), result: $query);

        $group   = $query['group'] ?? null;
        $name    = $query['name'] ?? null;
        $created = $query['created'] ?? new \DateTime()->format(format: 'Y-m-d H:i:s');
        if (null === $group || null === $name) {
            throw new \InvalidArgumentException(message: "missing group or name", code: 400);
        }

        $prepared = $this->pdo->prepare(query: 'insert into deployments(name, group_name, created_at) values (:name, :group, :created)');
        $params   = [
            ':name'    => \base64_decode(string: $name),
            ':group'   => \base64_decode(string: $group),
            ':created' => $created,
        ];

        $success = $prepared->execute(params: $params);
        if (false === $success) {
            throw new \PDOException(message: "query failed", code: 500);
        }

        return $this->response->withStatus(code: 204);
    }
}
