<?php

$base = "karopapier.de";
$proto = "https://";
//Didis Testsystem
if ($_SERVER['HTTP_HOST'] == "www.panamapapier.de") {
    $base = "www.panamapapier.de";
}

$urls = array(
    "/api/users/1" => "APIv2",
    "/api/users/check" => "APIv2",
    "/api/map/1" => "APIv2",
    "/api/chat/last" => "APIv2",
    "/api/contacts" => "APIv2",
    "/api/messages" => "APIv2",
    "/api/chat?start=357194&limit=20" => "APIv2",
    "/api/chat?start=11965&limit=1" => "APIv2",
    "/api/user/773/dran" => "APIv2",
    "/api/user/773/dran.json" => "Legacy API, deprecated",
    "/api/user/Botrix/dran.json" => "Legacy API",
    "/api/user/check.json" => "APIv2, deprecated",
    "/api/user/blockerlist.json" => "Legacy API",
    "/api/user/1/blocker.json" => "Legacy API",
    "/api/game/44773/info.json" => "Legacy API",
    "/api/games/44773/info" => "Legacy API",
    "/api/game/44773/details.json" => "Legacy API",
    "/api/games/44773/details" => "Legacy API",
    "/api/user/list.json" => "Legacy API",
    "/api/map/list.json" => "Legacy API",
    "/api/map/list.json?nocode=true" => "Legacy API",
    "/api/map/list.json?players=30" => "Legacy API",
    "/api/mapcode/1.json" => "Legacy API",
    "/api/mapcode/1.txt" => "Legacy API",
    "/api/map/1/vote.json" => "Legacy API",
    "/api/map/1/vote.json?users=[1,773,213,516]" => "Legacy API",
    "/api/chat/users.json" => "Legacy API",
    "/api/chat/users" => "APIv2, planned",
    "/api/chat/list.json" => "Legacy API",
    "/api/chat/list.json?limit=1" => "Legacy API",
    "/api/chat/list.json?start=11965&limit=1" => "Legacy API",
    "/api/games?user=1" => "Legacy API",
    "/api/games?user=1&finished=true" => "Legacy API",
    "/api/games?user=1&finished=true&limit=1&offset=300" => "Legacy API",
    "/api/games?limit=2&offset=3000" => "Legacy API",
    "http://volkswurst.de/api/" => "kilis API",
    "/api/user/1" => "APIv2, deprecated",
    "/api/user/1/info.json" => "Legacy API, deprecated",
    "/api/user/Botrix" => "APIv2, deprecated",
    "/api/users/Botrix" => "APIv2, deprecated",
    "/api/user/Botrix/info.json" => "Legacy API, deprecated",
    "/api/map/1.json" => "Legacy API, deprecated",
);
?>
<!doctype html>
<html>
    <head>
        <title>Karo API Browser</title>
        <style type="text/css">
            body {
                background-color: #def;
                font-family: Arial;
            }

            a {
                text-decoration: none;
            }

            #jsonCode {
                border: 1px solid #000000;
                background-color: #fff;
                display: inline-block;
                overflow: auto;
            }

            .clearer {
                float: none;
                clear: both
            }

            #url {
                width: 400px
            }

            #container {
                display: flex;
            }

            .urls {
                flex: 1;
                max-width: 600px;
            }

            .result {
                flex: 1;
            }

            .apiUrl {
                font-size: 12px;
            }

            span.apiInfo {
                font-size: 11px;
            }
        </style>
    </head>
    <body>
        <h1>Karo API Browser</h1>
        <div id="container">
            <div class="urls">
                <ul>
                    <?php foreach ($urls as $url => $apiversion): ?>
                        <?php $tags = array(); ?>
                        <li>
                <?php
                if (strpos($apiversion, "v2")) {
                    $tags[] = "b";
                }
                if (strpos($apiversion, "deprecated")) {
                    $tags[] = "strike";
                }
                if (strpos($apiversion, "planned")) {
                    $tags[] = "i";
                }
                $link = '<a class="apiUrl" href="'.$url.'">'.$url.'</a>';
                $link .= ' <span class="apiInfo">('.$apiversion.')</span>';

                $out = '';
                foreach ($tags as $tag) {
                    $out .= '<'.$tag.'>';
                }
                $out .= $link;
                foreach ($tags as $tag) {
                    $out .= '</'.$tag.'>';
                }

                echo $out;

                ?>
            </li>
                    <?php endforeach; ?>
                </ul>
                <form onsubmit="apiCall(); return false;"><label for="jsonp">JSONP</label>
                    <input type="checkbox" name="jsonp"
                           id="jsonp" checked="checked"><br/>
                    <input type="text" name="url" id="url" value=""><input type="submit" value="Send"/>
                </form>
                <h3>Nich testbare POST calls</h3>
                <ul>
                    <li>/api/game/add.json -- Legacy API v1, nicht final</li>
                    <li>/api/login -- Man poste ein JSON array {"login":"Didi","password":"Karo4ever"} und erhält entweder error 403
            oder die API-Daten des User UND ein COOKIE namens KaroKeks (API v2)
        </li>
                </ul>
                <p style="width: 500px">Dies soll zur Dokumentation der bisherigen verfuegbaren API-Calls dienen. Alle Calls sollten
                    sowohl den XMLHttpRequest als auch JSONP unterstuetzen, also mit und ohne callback. Bei einigen fehlt das evtl.
                    noch. Auch sonst, falls gewisse Calls fehlen/sinnvoll waeren, bzw. mehr Daten geliefert werden sollten, bitte PER
                    EMAIL!</p>
            </div>
            <div class="result">
                <pre id="jsonCode">{}</pre>
            </div>
        </div>
        <div class="clearer"></div>
        <script type="text/javascript" src="//code.jquery.com/jquery-latest.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.apiUrl').click(function(e) {
                    var url = e.currentTarget.href;
                    if ($('#jsonp').prop('checked')) {
                        if (url.indexOf('?') === -1) {
                            url += "?";
                        } else {
                            url += "&";
                        }
                        url = url + 'callback=?';
                    }
                    $('#url').val(url);
                    //execApi(url);
                    apiCall(url);
                    e.preventDefault();
                })
            });

            function apiCall() {
                var url = $('#url').val();
                if (url) {
                    $('#jsonCode').text('Loading ' + url);
                    execApi(url);
                }
            }

            function execApi(url) {
                var jqxhr = $.getJSON(url, function(data) {
                    var txt = JSON.stringify(data, false, 4).replace(/\\r/g, "");
                    txt = txt.replace(/\\n/g, "\n");
                    $('#jsonCode').text(txt);
                })
                    .error(function(err) {
                        $('#jsonCode').html(err.responseText);
                    });
            }
        </script>
    </body>
</html>
