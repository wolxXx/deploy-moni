<?php
declare(strict_types=1);
/**
 * @var \Application\DataObject\Group[] $groups
 * @var \IntlDateFormatter $dateFormatter
 * @var \Application\DataObject\View\IndexAction $data
 */

?>

<? foreach ($data->groups as $group): ?>

    <div style="display: flex; flex-direction: column; width: 15%; border: 1px solid white; padding: 0.2%; word-wrap: anywhere;">
        <div style="text-align: left; font-weight: bold; font-size: 1.3em; padding-bottom: 2px;">
            <?= $group->name ?>
        </div>
        <div>
            <? foreach ($group->get() as $deployment): ?>

                <?
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
                        <?= $dateFormatter->format(datetime: new \DateTime(datetime: $deployment->createdAt)) ?>
                    </span>
                    <?= $deployment->name ?>

                    <style type="text/css">

                    </style>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const dialog = document.querySelector("dialog");
                            const showButton = document.querySelector("dialog + button");
                            const closeButton = document.querySelector("dialog button");
                            showButton.addEventListener("click", () => {
                                dialog.showModal();
                            });
                            closeButton.addEventListener("click", () => {
                                dialog.close();
                            });
                        });
                    </script>

                    <dialog>
                        <button autofocus>Close</button>
                        <p>This modal dialog has a groovy backdrop!</p>
                    </dialog>
                    <button>Show the dialog</button>
                </div>
            <? endforeach ?>
        </div>
    </div>
<? endforeach ?>

<script>
    window.setTimeout(function() {
        location.reload();
    }, 20000);
</script>
