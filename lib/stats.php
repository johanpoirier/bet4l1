<?
class Stats {
	var $parent;

	function Stats(&$parent) {
		$this->parent = $parent;
	}

  function getUserStats($userID) {
    $req = "SELECT *, label FROM ".$this->parent->config['db_prefix']."stats_user";
    $req .= " WHERE userID = ".$userID;
    
    $stats = $this->parent->db->select_array($req, $nb_stats);
    
    return $stats;
  }
  
  function getUserStatsMaxOf($data) {
    $req = "SELECT MAX($data) FROM ".$this->parent->config['db_prefix']."stats_user";
    $max = $this->parent->db->select_one($req);

    return $max;
  }

  function createStats() {
    $users = $this->loadRanking();
    
    $reqBase = "INSERT INTO  ".$this->parent->config['db_prefix']."stats_user VALUES";
    foreach($users as $user) {
      $req = $reqBase." (".$user['ID'].", NOW(), ".$user['RANK'].", ".$user['POINTS'].", ".$user['NBRESULTS'].", ".$user['NBSCORES'].", ".$user['DIFF'].")";
      $this->parent->db->insert($req);
    }
    echo "OK";
    
    return;
  }
  
  function regenerateStats() {
    // empty the table
    $this->parent->db->exec_query("DELETE FROM ".$this->parent->config['db_prefix']."stats_user");
    
    // ranking snapshot for each phase
    $reqBase = "INSERT INTO  ".$this->parent->config['db_prefix']."stats_user VALUES";
    $phases = $this->parent->phases->getPlayedOnes('ASC');
    $globalUsersRanking = array();
    foreach($phases as $phase) {
      $globalUsersRanking = $this->addPhaseRanksToGlobalRanking($globalUsersRanking, $this->parent->users->getRankingByPhase($phase['phaseID']));
      usort($globalUsersRanking, "compare_users");
      $rank = 0;
      foreach($globalUsersRanking as $user) {
        $rank++;
        $req = $reqBase." (".$user['userID'].", '".$phase['name']."', $rank, ".$user['points'].", ".$user['nbresults'].", ".$user['nbscores'].", ".$user['diff'].")";
        $this->parent->db->insert($req);
      }
    }
  
    return;
  }
  
  function addPhaseRanksToGlobalRanking($usersRanking, $ranksToAdd) {
    if(sizeof($usersRanking) == 0) {
      $usersRanking = $ranksToAdd;
    }
    else {
      for($i = 0; $i < sizeof($usersRanking); $i++) {
        $usersRanking[$i]['points'] += $ranksToAdd[$usersRanking[$i]['userID']]['points'];
        $usersRanking[$i]['nbresults'] += $ranksToAdd[$usersRanking[$i]['userID']]['nbresults'];
        $usersRanking[$i]['nbscores'] += $ranksToAdd[$usersRanking[$i]['userID']]['nbscores'];
        $usersRanking[$i]['diff'] += $ranksToAdd[$usersRanking[$i]['userID']]['diff'];
      }
    }
    return $usersRanking;
  }
}
?>
