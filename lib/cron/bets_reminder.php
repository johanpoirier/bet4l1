#!/usr/local/bin/php
<?

header("Content-Type: text/plain; charset=utf-8");

define('WEB_PATH', "/");
define('BASE_PATH', "../../");
define('URL_PATH', "/");

require( BASE_PATH . 'lib/engine.php');

$debug = false;
$engine = new Engine(false, $debug);

$nbDaysToCheck = 1;
if ($engine->games->getNbMatchsInTheNextNDays($nbDaysToCheck) > 0) {
    $users = $engine->users->getActiveUsersWhoHaveNotBet($nbDaysToCheck);
    foreach ($users as $user) {
        echo $user['email'] . "\n";
        utf8_mail($user['email'], "Pronos L1 - Matchs à pronostiquer", "Bonjour " . $user['login'] . ",\n\nIl y a des matchs dans moins de 48H et vous n'avez toujours pas pronostiqué !\n\nRendez-vous sur " .$engine->config['url'] . " pour voter.\n\nCordialement,\nL'équipe " . $engine->config['support_team'] . "\n", $engine->config['title'], $engine->config['support_email'], $engine->config['email_simulation']);
    }
    echo "\nOK\n";
}
else {
    echo "No games\n";
}
?>