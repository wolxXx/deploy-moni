<?php

declare(strict_types=1);

namespace Application\Action;

class IndexAction extends AbstractAction
{

    public function run(): \Psr\Http\Message\ResponseInterface
    {
        $groups     = [];
        $groupNames = $this
            ->pdo
            ->query(
                query: <<<SQL
                    SELECT 
                        group_name 
                    FROM 
                        deployments deployment 
                    GROUP BY 
                        deployment.group_name 
                    ORDER BY 
                        deployment.group_name ASC
                    ;
                    SQL
            )
            ->fetchAll()
        ;
        foreach ($groupNames as $groupName) {
            $group    = new \Application\DataObject\Group(name: $groupName['group_name']);
            $groups[] = $group;
            $data     = $this
                ->pdo
                ->query(query: 'select * from deployments where group_name = "' . $groupName['group_name'] . '" order by created_at DESC, id desc')
                ->fetchAll()
            ;
            foreach ($data as $deployment) {
                $group->add(deployment: new \Application\DataObject\Deployment(id: $deployment['id'], name: $deployment['name'], createdAt: $deployment['created_at']));
            }
        }

        $view = new \Application\DataObject\View\IndexAction();
        $view->groups        = $groups;

        return $this->render(
            template: 'action' . \DIRECTORY_SEPARATOR . 'index.php',
            view    : $view
        );
    }
}