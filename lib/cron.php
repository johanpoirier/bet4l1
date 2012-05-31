<?
	define('WEB_PATH', "/");
	define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']."/");
	define('URL_PATH', "/");

	require('../../../include/classes/Engine.php');

	$debug = false;
	$engine = new Engine(false, $debug);

  define('PHASE_ID_ACTIVE', $engine->getPhaseIDActive());

  if($engine->getNbMatchsFromPhaseInNextDays(PHASE_ID_ACTIVE, 2) > 0) {
    //$users = $engine->getUsersMissingPronos(PHASE_ID_ACTIVE);
    $users = $engine->getUsersMissingPronosByMatch(162);
    
    $objet = "[CLAR] Pronostics L1";
    $headers = "From: Pronos L1 - CLAR <noreply@nirgal.org>\n";
    $headers .= "X-Sender: <noreply@nirgal.org>\n";
    $headers .= "Return-Path: <noreply@nirgal.org>\n";
  
    $contenu = "";
  	foreach($users as $user) {
      if($user['email_preferences'][0] == "1") {
        $dest = $user['email'];
        $contenu = "Bonjour ".$user['login'].",\n\n";
        $contenu .= "La journée de L1 commence dans quelques heures.\nPense à voter !\n\n";
        //$contenu .= "Le match FCG Bordeaux - Valenciennes FC a été avancé à ce vendredi 5 à 19h.\nPense à voter !\n\n";
        $contenu .= "Les administrateurs du site http://pronos-l1.nirgal.org/";

        if(@mail($dest, $objet, stripslashes($contenu), $headers)) echo "sent to ".$user['login']." at ".$user['email']."<br />";
      }
      else {
        echo "not sent to ".$user['login']." at ".$user['email']."<br />";
      }
    }
    @mail("johan.poirier@gmail.com", $objet, stripslashes($contenu), $headers);
  }
?>
