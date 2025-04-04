<?php
declare(strict_types=1);
/**
 * @var \Application\DataObject\View\IndexAction $view
 */

?>
<dialog style="min-width: 400px;">
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
            if(true === modalOpen) {
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
                let innerHTML = querySelector.innerHTML;
                dialog.innerHTML = innerHTML;
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
                dialog.showModal();
                window.clearTimeout(timeoutHandler);
                modalOpen = true

                return false;
            })
        })

    });
</script>

<? foreach ($view->groups as $group): ?>

    <?
        $count = 0;
    ?>
    <div style="display: flex; flex-direction: column; width: 15%; border: 1px solid white; padding: 0.2%; word-wrap: anywhere;" class="group">
        <div style="text-align: left; font-weight: bold; font-size: 1.3em; padding-bottom: 2px;">
            <?= $group->name ?>: <?= count($group->get()) ?>
            <button class="showDeployments" style="margin-left: 10px;">
                show
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

            <? foreach ($group->get() as $deployment): ?>
                <div>
                    <span style="color: #ccc; font-size: 0.79em; margin-right: 5px; display: inline-block;">
                        <?= $view->dateFormatter->format(datetime: new \DateTime(datetime: $deployment->createdAt)) ?>
                    </span>
                    <?= $deployment->name ?>
                </div>
            <? endforeach ?>
        </div>

        <div>

            <? foreach ($group->get() as $deployment): ?>

                <?
                if(++$count > 3 ) {
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
                    <span style="color: #ccc; font-size: 0.79em; margin-right: 5px; display: inline-block;">
                        <?= $view->dateFormatter->format(datetime: new \DateTime(datetime: $deployment->createdAt)) ?>
                    </span>
                    <?= $deployment->name ?>


                </div>
            <? endforeach ?>
        </div>
    </div>
<? endforeach ?>

