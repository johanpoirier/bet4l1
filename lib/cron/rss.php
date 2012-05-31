#!/usr/local/bin/php
<?php

header("Content-Type: text/plain; charset=utf-8");

define('WEB_PATH', "/");
define('BASE_PATH', "../../");
define('URL_PATH', "/");

require( BASE_PATH . 'lib/engine.php');

$debug = false;
$engine = new Engine(false, $debug);

define('MAGPIE_DIR', '../rss/');
require_once(MAGPIE_DIR . 'rss_fetch.inc');

$url = "http://www.matchendirect.fr/rss/foot-ligue-1-c10.xml";
$rss = fetch_rss($url);

foreach ($rss->items as $item) {
    $content = $item['title'];
    if (preg_match("/^Ligue 1 : ([a-zA-Z\- ]*) - ([a-zA-Z\- ]*) \(score final : ([0-9])-([0-9])/", $content, $vars)) {
        $match = $engine->games->getByTeamRssNames($vars[1], $vars[2]);
        if($match) {
            if($match['scoreMatchA'] == null) {
                $engine->games->saveResult($match['matchID'], $match['teamAid'], $match[3]);
                //echo "engine->games->saveResult(" . $match['matchID'] . ", " . $match['teamAid'] . ", ". $vars[3] . ");\n";
            }
            if($match['scoreMatchB'] == null) {
                $engine->games->saveResult($match['matchID'], $match['teamBid'], $match[4]);
                //echo "engine->games->saveResult(" . $match['matchID'] . ", " . $match['teamBid'] . ", ". $vars[4] . ");\n";
            }
        }
    }
}
?>