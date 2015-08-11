<?php
date_default_timezone_set('Europe/Paris');

session_start();

header("Content-Type: text/html; charset=utf-8");
define('WEB_PATH', "/");
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . "/");
define('URL_PATH', "/");

require('lib/engine.php');

$debug = false;
$engine = new Engine(false, $debug);
$message = "";

define('LOGIN', (isset($_GET['op']) && ($_GET['op']) == "login"));
define('FORGOT_IDS', (isset($_GET['op']) && ($_GET['op']) == "forgot_ids"));
define('REGISTER', (isset($_GET['op']) && ($_GET['op']) == "register"));
define('AUTHENTIFICATION_NEEDED', (!isset($_SESSION['userID']) && !LOGIN && !REGISTER && !FORGOT_IDS));
$phaseIdActive = $engine->phases->getPhaseIDActive();
if(!$phaseIdActive) {
    $phaseIdActive = -1;
}
define('PHASE_ID_ACTIVE', $phaseIdActive);

if(isset($_GET['phase'])) {
    $phaseID = $_GET['phase'];
} else if($engine->phases->isLast(PHASE_ID_ACTIVE) || $engine->phases->isFirst(PHASE_ID_ACTIVE)) {
    $phaseID = PHASE_ID_ACTIVE;
}

if(FORGOT_IDS) {
    if(isset($_POST['email'])) {
        $res = $engine->sendIDs($_POST['email']);
        redirect("/?message=" . $res);
    } else {
        $engine->loadHeader();
        $engine->loadMenu();
        $engine->loadForgotIDs();
        $engine->loadFooter();
        $engine->display();
        exit();
    }
}

if(REGISTER) {
    if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['login'])) {
        if($_POST['password1'] != $_POST['password2']) {
            redirect("?op=register&message=".PASSWORD_MISMATCH);
        }
        $status = $engine->users->add($_POST['login'], $_POST['password1'], $_POST['name'], $_POST['firstname'], $_POST['email'], '', 0);

        if($status < 0) {
            redirect("?op=register&c=".$_POST['code']."&message=".$status);
        }
        else {
            redirect("/?message=".REGISTER_OK);
        }
    }
    if(isset($_GET['w']) && $_GET['w']) {
        $w = $bet->lang['messages'][$_GET['message']];
    }

    $engine->loadHeader();
    $engine->loadMenu();
    $engine->loadRegister($message);
    $engine->loadFooter();
    $engine->display();
    exit();
}

if(AUTHENTIFICATION_NEEDED) {
    if(isset($_GET['message']) && $_GET['message']) {
        $message = $engine->lang['messages'][$_GET['message']];
    }
    $engine->loadHeader();
    $engine->loadMenu();
    $engine->loadLogin($message);
    $engine->loadFooter();
    $engine->display();
    exit();
}

$op = "";
if(isset($_REQUEST['op'])) {
    $op = $_REQUEST['op'];
}

// db actions
switch($op) {
    case "save_results":
        if($engine->isAdmin()) {
            foreach($_POST as $input => $score) {
                $ipt = strtok($input, "_");
                if($ipt == "iptScoreTeam") {
                    $team = strtok("_");
                    $matchID = strtok("_");
                    $engine->games->saveResult($matchID, $team, $score);
                }
            }
            $op = "edit_results";
        }
        break;

    case "add_phase":
        $phaseLabel = $_POST['phase_label'];
        $engine->phases->add($phaseLabel);
        $op = "edit_games";
        break;

    case "move_phase":
        $phaseIdToMove = $_POST['phaseToMove'];
        $phaseRef = $_POST['phaseRef'];
        $engine->phases->move($phaseIdToMove, $phaseRef);
        $op = "edit_games";
        //return;
        break;

    case "add_match":
        $submit = $_POST['add_match'];
        $id_match = $_POST['idMatch'];
        if($submit == "Supprimer") {
            $engine->games->delete($id_match);
        } else {
            $isSpecial = 0;
            if(isset($_POST['matchspecial']))
                $isSpecial = $_POST['matchspecial'];
            $engine->games->add($_POST['phase'], $_POST['day'], $_POST['month'], $_POST['year'], $_POST['hour'], $_POST['minutes'], $_POST['teamA'], $_POST['teamB'], $isSpecial, $_POST['idMatch']);
        }
        $op = "edit_games";
        break;

    case "save_pronos":
        $userId = $_REQUEST['userId'];
        foreach($_POST as $input => $score) {
            $ipt = strtok($input, "_");
            if($ipt == "iptScoreTeam") {
                $team = strtok("_");
                $matchID = strtok("_");
                if(!$engine->games->isDatePassed($matchID)) {
                    $engine->bets->save($userId, $matchID, $team, $score);
                } else {
                    $user = $engine->users->getById($userId);
                    if($engine->isAdmin() && ($userId != $_SESSION['userID'])) {
                        $engine->bets->save($userId, $matchID, $team, $score);
                    }
                }
            }
        }
        $op = "edit_pronos";
        break;

    case "add_user_team":
        $engine->users->addTeam($_POST['user_team_name']);
        $op = "edit_users";
        break;

    case "add_user":
        $submit = $_POST['add_user'];
        $login = $_POST['login'];
        if($submit == "Supprimer") {
            $engine->users->delete($login);
        } else {
            $name = $_POST['name'];
            $pass = $_POST['pass'];
            $mail = $_POST['mail'];
            $userTeamId = $_POST['sltUserTeam'];
            $isAdmin = 0;
            if(isset($_POST['admin'])) {
                $isAdmin = $_POST['admin'];
            }
            $engine->users->addOrUpdate($login, $pass, $name, $mail, $userTeamId, $isAdmin);
        }

        $op = "edit_users";
        break;

    case "add_instance":
        $name = $_POST['name'];
        if(strlen($name) > 3) {
            $parentId = $_POST['parentId'];
            $copyData = 0;
            if(isset($_POST['copyData'])) {
                $copyData = $_POST['copyData'];
            }
            $engine->instances->add($name, $_SESSION['userID'], $parentId, $copyData);
        }

        $op = "edit_instances";
        break;

    case "edit_instance":
        $submit = $_POST['edit_instance'];
        $id = $_POST['id'];
        $name = $_POST['name'];

        if($submit == "Modifier" && strlen($name) > 3) {
            $active = 0;
            if(isset($_POST['active'])) {
                $active = $_POST['active'];
            }
            $engine->instances->update($id, $name, $active);
        }
        else {
            // delete instance
        }

        $op = "edit_instances";
        break;
}

// page display
$engine->loadHeader(isset($_SESSION['userID']));
$engine->loadMenu();
switch($op) {
    case "login":
        if($engine->login($_POST['login'], $_POST['pass'])) {
            redirect("/");
        } else {
            $engine->loadLogin("Le login et/ou le mot de passe sont incorrects.");
        }
        break;

    case "logout":
        session_destroy();
        redirect("/");
        break;

    case "view_ranking":
        $instanceID = false;
        if(isset($_GET['instance'])) {
            $instanceID = $_GET['instance'];
        }
        $engine->loadRanking($_SESSION['userID'], $instanceID);
        break;

    case "view_ranking_lcp":
        $engine->loadRankingLCP($_SESSION['userID']);
        break;

    case "view_ranking_perfect":
        $engine->loadRankingPerfect($_SESSION['userID']);
        break;

    case "view_ranking_phase":
        $engine->loadRankingByPhase($phaseID);
        break;

    case "view_ranking_phase_lcp":
        $engine->loadRankingLCPByPhase($phaseID);
        break;

    case "view_ranking_phase_perfect":
        $engine->loadRankingPerfectByPhase($phaseID);
        break;

    case "view_ranking_visual":
        $engine->loadRankingVisual($_SESSION['userID']);
        break;

    case "update_ranking":
        $engine->users->updateRanking();
        //$engine->users->updateTeamRanking();
        $engine->users->updateRankingLCP();
        $engine->loadRanking($_SESSION['userID']);
        break;

    case "stats":
        $engine->stats->regenerateStats();
        $engine->loadRanking($_SESSION['userID']);
        break;

    case "my_profile":
        $engine->loadMyProfile($_SESSION['userID']);
        break;

    case "palmares":
        $engine->loadPalmares();
        break;

    case "update_profile":
        $message = "";
        $pwd = "";
        if((strlen($_POST['pwd1']) > 0)) {
            if($_POST['pwd1'] == $_POST['pwd2'])
                $pwd = $_POST['pwd1'];
            else
                $message = "Les 2 mots de passe sont différents !";
        }
        $email_preferences = "00";
        if(isset($_POST['mail_1']) && ($_POST['mail_1'] == "1"))
            $email_preferences[0] = "1";
        if(isset($_POST['mail_2']) && ($_POST['mail_2'] == "1"))
            $email_preferences[1] = "1";
        if(!$engine->users->updateProfile($_SESSION['userID'], $_POST['name'], $_POST['email'], $_POST['pwd1'], $email_preferences)) {
            $message = "La mise à jour du profil n'a pas effectuée. Contactez l'administrateur.";
        }
        $engine->loadMyProfile($_SESSION['userID'], $message);
        break;

    case "view_results":
        $engine->loadResults($phaseID);
        break;

    case "view_user_stats":
        $userId = $_SESSION['userID'];
        if(isset($_REQUEST['user']))
            $userId = $_REQUEST['user'];
        $engine->loadUserStats($userId);
        break;

    case "edit_pronos":
        $userId = $_SESSION['userID'];
        if(isset($_REQUEST['user'])) {
            $userId = $_REQUEST['user'];
        }
        $mode = 0;
        if($userId != $_SESSION['userID']) {
            $mode = 1;
        }
        if(($_SESSION['status'] == 1) && ($userId != $_SESSION['userID'])) {
            $mode = 2;
        }
        if(!isset($_GET['phase'])) {
            $phaseID = $engine->phases->getNextPhaseIdToBet();
        }
        $engine->loadBets($userId, $phaseID, $mode);
        break;

    case "rules":
        $engine->loadRules();
        break;

    case "edit_users":
        if($engine->isAdmin()) {
            $engine->loadUsers();
        }
        break;

    case "edit_games":
        if($engine->isAdmin()) {
            $engine->loadGames();
        }
        break;

    case "edit_results":
        if($engine->isAdmin()) {
            $engine->loadEditResults();
        }
        break;

    case "forgot_password":
        if($debug)
            echo "FORGOT_PASSWORD<br />";
        if(isset($_POST['login'])) {
            if($bet->send_password($_POST['login']))
                redirect("?w=1");
            else
                redirect("?w=2");
        }
        else {
            $engine->loadForgotPassword();
        }
        break;

    case "edit_instances":
        if($engine->isAdmin()) {
            $engine->loadEditInstances();
        }
        break;

    default:
        $engine->loadRanking($_SESSION['userID']);
        break;
}
$engine->loadFooter(true);
$engine->display();

