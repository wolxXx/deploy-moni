
<!DOCTYPE html>
<html lang="en" translate="no">
    <head>
        <meta name="google" content="notranslate">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/styles.css?t=<?= filemtime('html/styles.css') ?>" media="screen" rel="stylesheet" type="text/css"/>
        <title>Deployment Monitor</title>
        <script>
            window.setInterval(function () {
                const event = new Date();
                console.log(event.toLocaleTimeString('de-DE'));
                document.getElementById('clock').innerHTML = event.toLocaleDateString('de-DE') + ' '+  event.toLocaleTimeString('de-DE');
            }, 150);
            window.setTimeout(function() {
                location.reload();
            }, 20000);
        </script>
    </head>
    <body>
        <div id="container">
                <div style="font-size: 1rem; display: flex; flex-wrap: wrap; width: 100%; gap: 5px; justify-content: left; overflow: hidden;">

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

                <?= $content ?>

                <span id="clock" style="position: fixed; bottom: 0; right: 0; background: #222; padding-left: 10px; padding-top: 10px; border-top: solid 1px #666; border-left: solid 1px #666; border-radius: 4px 0 0 0;"></span>
            </div>
        </div>
    </body>
</html>
