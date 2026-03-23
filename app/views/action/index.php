<?php
declare(strict_types=1);
/**
 * @var \Application\DataObject\View\IndexAction $view
 */

?>
<dialog>
    <button autofocus>Close</button>
    <p>This modal dialog has a groovy backdrop!</p>
</dialog>

<script>
    let timeoutHandler = null;
    let modalOpen = false;
    function initReload() {
        timeoutHandler = window.setTimeout(function() {
            location.reload();
        }, 20000);
    }


    document.addEventListener('DOMContentLoaded', function () {
        initReload()
        const dialog = document.querySelector("dialog");



        document.addEventListener('click', function (event) {
            return true;
            if (true === modalOpen) {
                event.stopPropagation();
                event.preventDefault();
                dialog.close()
                initReload()
                modalOpen = false

                return false
            }
        })

        document.querySelectorAll('.showDeployments').forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.stopPropagation();
                event.preventDefault();
                let closest = element.closest('.group');
                let querySelector = closest.querySelector('.modalContent');
                dialog.innerHTML = querySelector.innerHTML;
                document.querySelector('::backdrop')?.addEventListener('click', function () {
                    dialog.close()
                    initReload()
                    modalOpen = false

                })
                dialog.querySelector('.closeButton').addEventListener('click', function () {
                    dialog.close()
                    initReload()
                    modalOpen = false
                })

                dialog.querySelectorAll('.deleteItemButton').forEach(function (element) {
                    console.log(element);
                    element.addEventListener('click', function (event) {
                        const deploymentId = element.getAttribute('data-deployment');
                        console.log('clicked on delete button');
                        event.stopPropagation();
                        event.preventDefault();
                        let closest = element.closest('.deploymentContainer');
                        closest.remove()

                        fetch('/api/v1/items/__ID__'.replace('__ID__', deploymentId), {
                            method: 'DELETE',
                        })

                        return false;
                    })
                })

                dialog.querySelectorAll('.deleteGroupButton').forEach(function (element) {
                    console.log(element);
                    element.addEventListener('click', function (event) {
                        const groupId = element.getAttribute('data-group');
                        console.log('clicked on delete button');
                        event.stopPropagation();
                        event.preventDefault();
                        dialog.close()
                        modalOpen = false
                        document.querySelector('.group[data-group="'+groupId+'"]').remove()
                        fetch('/api/v1/groups/__ID__'.replace('__ID__', groupId), {
                            method: 'DELETE',
                        })
                        return false;
                    })
                })

                dialog.showModal();
                window.clearTimeout(timeoutHandler);
                modalOpen = true


                return false;
            })
        })

    });
</script>

<?php foreach ($view->groups as $group): ?>

    <?php
        $count = 0;
    ?>
    <div style="" class="group" data-group="<?= base64_encode($group->name) ?>">
        <div style="">
            <?= $group->name ?>
            <button class="showDeployments">
                show <?= count($group->get()) ?>
            </button>
        </div>

        <div class="modalContent" style="display: none; width: 100%;">
            <h1>
                <?= $group->name ?>
            </h1>
            <div style="width: 100%; text-align: center">
                <button class="closeButton">
                    close
                </button>
            </div>
            <div style="width: 100%; text-align: center">
                <button class="deleteGroupButton" data-group="<?= base64_encode($group->name) ?>">
                    delete group
                </button>
            </div>

            <?php foreach ($group->get() as $deployment): ?>
                <div class="deploymentContainer">
                    <span class="deleteItemButton" data-deployment="<?= $deployment->id ?>">
                        <button class="danger">
                            X
                        </button>
                    </span>
                    <span class="deployment">
                        <?= $view->dateFormatter->format(datetime: new \DateTime(datetime: $deployment->createdAt)) ?>
                    </span>
                    <?= $deployment->name ?>
                </div>
            <?php endforeach ?>
        </div>

        <div>
            <?php foreach ($group->get() as $deployment): ?>
                <?php
                    if (++$count > 2 ) {
                        break;
                    }
                    $style = ' style="';
                    if ($deployment->id !== count(value: $group->get()) - 1) {
                        $style .= ' border-bottom: 1px solid #cccccc60;';
                    }
                    if (0 === $deployment->id) {
                        $style .= ' font-size: 1.1em; font-weight: bold; color: #fff;';
                    }
                    if (0 !== $deployment->id) {
                        $style .= ' color: #ccc;';
                    }
                    $style .= ' word-wrap: anywhere;"';
                ?>
                <div <?= $style ?>>
                    <span class="deployment">
                        <?= $view->dateFormatter->format(datetime: new \DateTime(datetime: $deployment->createdAt)) ?>
                    </span>
                    <span style="max-width: 150px; overflow:hidden; display: inline-block; text-wrap: nowrap;">
                        <?= $deployment->name ?>
                    </span>


                </div>
            <?php endforeach ?>
        </div>
    </div>
<?php endforeach ?>

