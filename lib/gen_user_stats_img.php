<?

Header("Content-Type: image/png");

define('WEB_PATH', "/");
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . "/");
define('URL_PATH', "/");

require(BASE_PATH . 'lib/engine.php');

$debug = false;
$engine = new Engine(false, $debug);

if (isset($_GET['user'])) {
    $userID = $_GET['user'];
    $user = $engine->users->getById($userID);
    $userStats = $engine->stats->getUserStats($userID);

    $type = $_GET['type'];

    $width = 550;
    $height = 320;
    $img = ImageCreate($width, $height);

    // definition des couleurs
    $white = ImageColorAllocate($img, 255, 255, 255);
    $black = ImageColorAllocate($img, 0, 0, 0);
    $green = ImageColorAllocate($img, 34, 139, 34);
    $red = ImageColorAllocate($img, 255, 0, 0);
    $blue = ImageColorAllocate($img, 0, 0, 255);

    // rempli en blance
    ImageFill($img, 0, 0, $white);

    if (sizeof($userStats) > 0) {
        // parametres initiaux
        $last_stat = $userStats[0];
        $day = 1;
        $marge_x_left = 20;
        $marge_x_right = 10;
        $marge_y_up = 15;
        $marge_y_down = 90;

        // real surface
        $draw_width = $width - ($marge_x_left + $marge_x_right);
        $draw_height = $height - ($marge_y_up + $marge_y_down);

        // type de stats
        if ($type == 1) {
            // rank type
            $max_x = sizeof($userStats);
            if ($max_x < 10)
                $max_x = 10;
            $max_y = $engine->users->getNumberOfActiveOnes();

            // repere
            ImageLine($img, $marge_x_left, ($draw_height + $marge_y_up), ($width - $marge_x_right), ($draw_height + $marge_y_up), $black);
            ImageLine($img, $marge_x_left, ($draw_height + $marge_y_up), $marge_x_left, $marge_y_up, $black);
            ImageString($img, 1, 5, (1 + $marge_y_up), "1", $black);
            ImageString($img, 1, 2, ($draw_height + $marge_y_up - 5), $max_y, $black);

            foreach ($userStats as $stat) {
                // computing coordinates
                $x_start = (($draw_width * ($day - 1)) / $max_x) + $marge_x_left;
                $x_end = (($draw_width * $day) / $max_x) + $marge_x_left;
                $y_start = (($draw_height * $last_stat['rank']) / $max_y) + $marge_y_up;
                $y_end = (($draw_height * $stat['rank']) / $max_y) + $marge_y_up;

                // draw line
                ImageLine($img, $x_start, $y_start, $x_end, $y_end, $red);
                ImageString($img, 1, ($x_end - 7), ($y_end - 10), $stat['rank'], $red);
                //ImageStringUp($img, 1, ($x_end - 7), ($draw_height + $marge_y_up + 88), $stat['label'], $black);
                imagettftext($img, 9, 90, ($x_end - 7), ($draw_height + $marge_y_up + 88), $black, BASE_PATH.'lib/arial.ttf', $stat['label']);

                $day++;
                $last_stat = $stat;
            }
        } elseif ($type == 2) {
            // moy points type
            $max_x = sizeof($userStats);
            if ($max_x < 10)
                $max_x = 10;
            $max_y = 16;
            $last_moy = 0;

            // repere
            ImageLine($img, $marge_x_left, ($draw_height + $marge_y_up), ($width - $marge_x_right), ($draw_height + $marge_y_up), $black);
            ImageLine($img, $marge_x_left, ($draw_height + $marge_y_up), $marge_x_left, $marge_y_up, $black);
            ImageString($img, 1, 5, ($draw_height + $marge_y_up - 5), "0", $black);
            ImageString($img, 1, 2, (1 + $marge_y_up), $max_y, $black);

            foreach ($userStats as $stat) {
                // moy
                if ($day == 1) {
                    $moy_day = $stat['points'];
                    $last_moy = $moy_day;
                }
                else
                    $moy_day = ($stat['points'] - $last_stat['points']);

                // computing points
                $x_start = (($draw_width * ($day - 1)) / $max_x) + $marge_x_left;
                $x_end = (($draw_width * $day) / $max_x) + $marge_x_left;
                $y_start = $draw_height - (($draw_height * $last_moy) / $max_y) + $marge_y_up;
                $y_end = $draw_height - (($draw_height * $moy_day) / $max_y) + $marge_y_up;

                // draw line
                ImageLine($img, $x_start, $y_start, $x_end, $y_end, $red);
                ImageString($img, 1, ($x_end - 5), ($y_end - 12), $moy_day, $red);
                //ImageStringUp($img, 1, ($x_end - 7), ($draw_height + $marge_y_up + 88), $stat['label'], $black);
                imagettftext($img, 9, 90, ($x_end - 7), ($draw_height + $marge_y_up + 88), $black, BASE_PATH.'lib/arial.ttf', $stat['label']);

                $day++;
                $last_stat = $stat;
                $last_moy = $moy_day;
            }
        }
        elseif ($type == 3) {
            // nbresults type
            $max_x = sizeof($userStats);
            if ($max_x < 10)
                $max_x = 10;
            //$max_y = $engine->getUserStatsMaxOf("nbresults");
            $max_y = $engine->stats->getUserStatsMaxOf("points");

            // repere
            ImageLine($img, $marge_x_left, ($draw_height + $marge_y_up), ($width - $marge_x_right), ($draw_height + $marge_y_up), $black);
            ImageLine($img, $marge_x_left, ($draw_height + $marge_y_up), $marge_x_left, $marge_y_up, $black);
            ImageString($img, 1, 5, ($draw_height + $marge_y_up - 5), "0", $black);
            ImageString($img, 1, 2, (1 + $marge_y_up), $max_y, $black);

            foreach ($userStats as $stat) {
                // computing coordinates
                $x_start = (($draw_width * ($day - 1)) / $max_x) + $marge_x_left;
                $x_end = (($draw_width * $day) / $max_x) + $marge_x_left;
                $y_start = $draw_height - (($draw_height * $last_stat['nbresults']) / $max_y) + $marge_y_up;
                $y_end = $draw_height - (($draw_height * $stat['nbresults']) / $max_y) + $marge_y_up;

                // draw line
                ImageLine($img, $x_start, $y_start, $x_end, $y_end, $red);
                ImageString($img, 1, ($x_end - 7), ($y_end - 10), $stat['nbresults'], $red);
                //ImageStringUp($img, 1, ($x_end - 7), ($draw_height + $marge_y_up + 88), $stat['label'], $black);
                imagettftext($img, 9, 90, ($x_end - 7), ($draw_height + $marge_y_up + 88), $black, BASE_PATH.'lib/arial.ttf', $stat['label']);

                // computing coordinates
                $y_start2 = $draw_height - (($draw_height * $last_stat['nbscores']) / $max_y) + $marge_y_up;
                $y_end2 = $draw_height - (($draw_height * $stat['nbscores']) / $max_y) + $marge_y_up;

                // draw line
                ImageLine($img, $x_start, $y_start2, $x_end, $y_end2, $green);
                ImageString($img, 1, ($x_end - 7), ($y_end2 - 10), $stat['nbscores'], $green);

                // computing coordinates
                $y_start3 = $draw_height - (($draw_height * $last_stat['points']) / $max_y) + $marge_y_up;
                $y_end3 = $draw_height - (($draw_height * $stat['points']) / $max_y) + $marge_y_up;

                // draw line
                ImageLine($img, $x_start, $y_start3, $x_end, $y_end3, $blue);
                ImageString($img, 1, ($x_end - 7), ($y_end3 - 10), $stat['points'], $blue);

                $day++;
                $last_stat = $stat;
            }
        }
    }

    ImagePNG($img);
    ImageDestroy($img);
}
?>