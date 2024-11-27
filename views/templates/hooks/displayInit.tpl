{if $apiKey}
    <script>
        window.lipscoreInit = function() {
            lipscore.init({
                apiKey: '{$apiKey}'
            });
        };
        (function() {
            var scr = document.createElement('script'); scr.async = 1;
            scr.src = "//static.lipscore.com/assets/{$locale}/lipscore-v1.js";
            document.getElementsByTagName('head')[0].appendChild(scr);
        })();
    </script>
{/if}