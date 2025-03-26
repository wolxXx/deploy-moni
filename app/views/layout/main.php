
<!DOCTYPE html>
<html lang="en" translate="no">
    <head>
        <meta name="google" content="notranslate">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/styles.css?t=<?= filemtime('public/styles.css') ?>" media="screen" rel="stylesheet" type="text/css"/>
        <title>Deployment Monitor</title>
        <script>
            window.setInterval(function () {
                const event = new Date();
                document.getElementById('clock').innerHTML = event.toLocaleDateString('de-DE') + ' '+  event.toLocaleTimeString('de-DE');
            }, 150);

        </script>
    </head>
    <body>
        <div id="container">
                <div style="font-size: 1rem; display: flex; flex-wrap: wrap; width: 100%; gap: 5px; justify-content: left; overflow: hidden;">
                    <?= $content ?>
                    <span id="clock"></span>
            </div>
        </div>
    </body>
</html>
