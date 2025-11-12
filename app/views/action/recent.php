<?php
declare(strict_types=1);
/**
 * @var \Application\DataObject\View\IndexAction $view
 */

?>


<script>
    let timeoutHandler = null;
    function initReload() {
        timeoutHandler = window.setTimeout(function() {
            location.reload();
        }, 20000);
    }
</script>

<table>
    <thead>
        <tr>
            <th>
                Group
            </th>
            <th>
                Name
            </th>
            <th>
                Name
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($view->groups as $group): ?>
            <tr>
                <td>
                    <?= $group->name ?>
                </td>
                <td>
                    <?= $group->get()[0]->name ?>
                </td>
                <td>
                    <?= $view->dateFormatter->format(datetime: new \DateTime(datetime: $group->get()[0]->createdAt)) ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>


