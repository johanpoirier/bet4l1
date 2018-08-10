<?php
  session_start();

  header("Content-Type: text/html; charset=utf-8");
	define('WEB_PATH', "/");
	define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']."/");
	define('URL_PATH', "/");

  require(BASE_PATH.'lib/engine.php');
  $engine = new Engine(false, false);

  $op = $_REQUEST['op'];
  switch($op) {
    case "getTags":
  		echo $engine->loadTags($_POST['userTeamID'], $_POST['start']);
  		break;

    case "getInfos":
  		echo $engine->loadInfos($_SESSION['userID']);
  		break;

    case "saveTag":
  		$tag = $_POST['tag'];
  		$teamID = -1;
  		if(isset($_POST['userTeamID'])) $teamID = $_POST['userTeamID'];
  		$engine->tags->save($tag, $teamID);
  		echo $engine->loadTags($teamID, 0);
  		break;

    case "delTag":
  		$tagID = $_POST['tagID'];
  		$engine->tags->delete($tagID);
  		$teamID = -1;
  		if(isset($_POST['userTeamID'])) {
                    $teamID = $_POST['userTeamID'];
                }
  		echo $engine->loadTags($teamID, 0);
  		break;

    case "getUser":
      $userID = $_REQUEST['id'];
      $user = $engine->users->getById($userID);
      if(isset($user)) echo $user['name']."|".$user['login']."|".$user['email']."|".$user['status']."|".$user['userTeamID'];
      break;

      case "getInstance":
          $id = $_REQUEST['id'];
          $instance = $engine->instances->getById($id);
          if(isset($instance)) echo $instance['id']."|".$instance['name']."|".$instance['ownerID']."|".$instance['parentID']."|".$instance['active'];
          break;

    case "getGame":
      $matchID = $_REQUEST['id'];
      $game = $engine->games->getById($matchID);
      if(isset($game)) echo intval(substr($game['date'], 8, 2))."|".intval(substr($game['date'], 5, 2))."|".substr($game['date'], 0, 4)."|".substr($game['date'], 11, 2)."|".substr($game['date'], 14, 2)."|".$game['status']."|".$game['phaseID']."|".$game['teamA']."|".$game['teamB']."|".$game['matchID'];
      break;

    default:
      break;
  }
