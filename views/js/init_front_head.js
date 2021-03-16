// LipScore init
 window.lipscoreInit = function() {
    lipscore.init({
        apiKey: lipscore_api_key
    });
 };
 (function() {
    var scr = document.createElement('script'); scr.async = 1;
    scr.src = "//static.lipscore.com/assets/no/lipscore-v1.js";
    document.getElementsByTagName('head')[0].appendChild(scr);
 })();
// END LipScore init