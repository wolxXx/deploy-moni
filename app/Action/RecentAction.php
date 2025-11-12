<?php

namespace Application\Action;

class RecentAction extends AbstractAction
{
    public function run(): \Psr\Http\Message\ResponseInterface
    {
        $view = new \Application\DataObject\View\IndexAction();
        $view->groups        = [];
        foreach ($this->pdo->query('SELECT DISTINCT group_name, created_at, name FROM deployments ORDER BY created_at DESC LIMIT 30') as $row) {
            $view->groups[] = new \Application\DataObject\Group(name: $row['group_name'])
                ->add(deployment: new \Application\DataObject\Deployment(id: 0, name: $row['name'], createdAt: $row['created_at']))
            ;
        }

        return $this->render(
            template: 'action' . \DIRECTORY_SEPARATOR . 'recent.php',
            view    : $view
        );
    }
}