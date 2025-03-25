<?php

declare(strict_types=1);

namespace Application\Action;

class RandomAction extends AbstractAction
{
    public function run(): \Psr\Http\Message\ResponseInterface
    {
        $this->pdo->query(query: 'delete from deployments');
        foreach (range(start: 0, end: 10) as $groupCounter) {
            $begin = new \DateTime(datetime: '2024-01-01 01:00:00');
            foreach (\range(start: 0, end: rand(min: 1, max: 100)) as $deploymentCounter) {
                $this->pdo->query(query: 'insert into deployments(name, group_name, created_at) values ("' . $deploymentCounter . '", "' . $groupCounter . '", "' . $begin->format(format: 'Y-m-d H:i:s') . '")');
                $begin->add(interval: new \DateInterval(duration: 'P1D'));
            }
        }

        return $this
            ->response
            ->withHeader(name: 'Location', value: '/')
            ->withStatus(code: 302)
        ;
    }
}