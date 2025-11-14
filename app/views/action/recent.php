<?php
declare(strict_types=1);
/**
 * @var \Application\DataObject\View\RecentAction $view
 */

?>

<style type="text/css">
    html, body {
        overflow: hidden;
    }
</style>

<script>
    let timeoutHandler = null;
    function initReload() {
        timeoutHandler = window.setTimeout(() => {
            location.reload();
        }, 20000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        initReload()
    });
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


