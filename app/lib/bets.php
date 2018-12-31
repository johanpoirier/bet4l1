<?php

class Bets {

    var $parent;

    function Bets(&$parent) {
        $this->parent = $parent;
    }

    function getNextOnesByUser($userID, $phaseID=-1) {
        // Main Query
        $req = "SELECT DISTINCT m.matchID, t1.name as teamAname, t2.name as teamBname, m.scoreA as scoreMatchA, m.scoreB as scoreMatchB, DATE_FORMAT(date, 'le %e/%m à %Hh') as date_str, TIME_TO_SEC(TIMEDIFF(m.date, NOW())) as delay_sec";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "pronos p ON(p.matchID = m.matchID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE m.matchID NOT IN (SELECT p.matchID FROM " . $this->parent->config['db_prefix'] . "pronos p WHERE p.userID = " . $userID . " AND p.scoreA IS NOT NULL AND p.scoreB IS NOT NULL) AND m.date > NOW()";
        if ($phaseID >= 0) {
            $req .= " AND m.phaseId = " . $phaseID;
        }
        $req .= " ORDER BY date_str ASC";

        $betCount = 0;
        $pronos = $this->parent->db->select_array($req, $betCount);

        if ($this->parent->debug) {
            array_show($pronos);
        }
        
        return $pronos;
    }

    function getOddsByGame($matchID) {
        /* $req = "SELECT *";
          $req .= " FROM ".$this->parent->config['db_prefix']."pronos p LEFT JOIN ".$this->parent->config['db_prefix']."users u ON(p.userID = u.userID)";
          $req .= " WHERE matchID = ".$matchID;
          $req .= " AND scoreA IS NOT NULL AND scoreB IS NOT NULL"; */

        $req = "SELECT p.userID, p.matchID, p.scoreA AS scorePronoA, p.scoreB as scorePronoB, m.scoreA as scoreMatchA, m.scoreB as scoreMatchB, u.name, u.login";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "pronos p";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "users u ON ( p.userID = u.userID )";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs m ON ( m.matchID = p.matchID)";
        $req .= " WHERE p.matchID = " . $matchID;
        $req .= " AND p.scoreA IS NOT NULL";
        $req .= " AND p.scoreB IS NOT NULL";
        $req .= " ORDER BY u.login";

        $pronos = $this->parent->db->select_array($req, $nb_bets);

        $odds = array();
        $odds['A_AVG'] = 0;
        $odds['B_AVG'] = 0;
        $odds['A_WINS'] = 0;
        $odds['B_WINS'] = 0;
        $odds['NUL'] = 0;
        $odds['EXACT_BETS'] = "";
        $odds['GOOD_BETS'] = "";
        $odds['NB_EXACT_BETS'] = "";
        $odds['NB_GOOD_BETS'] = "";

        $exact_bets = "";
        $good_bets = "";
        $nb_exact_bets = 0;
        $nb_good_bets = 0;

        foreach ($pronos as $prono) {
            if ($prono['scorePronoA'] > $prono['scorePronoB'])
                $odds['A_WINS']++;
            if ($prono['scorePronoB'] > $prono['scorePronoA'])
                $odds['B_WINS']++;
            if ($prono['scorePronoA'] == $prono['scorePronoB'])
                $odds['NUL']++;
            $odds['A_AVG'] += $prono['scorePronoA'];
            $odds['B_AVG'] += $prono['scorePronoB'];

            if (($prono['scoreMatchA'] == $prono['scorePronoA']) && ($prono['scoreMatchB'] == $prono['scorePronoB'])) {
                $exact_bets .= "<a href=\"/?op=edit_pronos&user=" . $prono['userID'] . "\">" . $prono['login'] . "</a><br />";
                $nb_exact_bets++;
            }
            $diffMatch = $prono['scoreMatchA'] - $prono['scoreMatchB'];
            $diffProno = $prono['scorePronoA'] - $prono['scorePronoB'];
            if ((($diffMatch == 0) && ($diffProno == 0))
                    || (($diffMatch > 0) && ($diffProno > 0))
                    || (($diffMatch < 0) && ($diffProno < 0))) {
                $good_bets .= "<a href=\"/?op=edit_pronos&user=" . $prono['userID'] . "\">" . $prono['login'] . "</a><br />";
                $nb_good_bets++;
            }
        }
        if ($nb_bets > 0)
            $odds['A_AVG'] = round($odds['A_AVG'] / $nb_bets, 2);
        if ($nb_bets > 0)
            $odds['B_AVG'] = round($odds['B_AVG'] / $nb_bets, 2);
        $odds['A_WINS'] = round(($nb_bets + 1) / ($odds['A_WINS'] + 1), 2);
        $odds['B_WINS'] = round(($nb_bets + 1) / ($odds['B_WINS'] + 1), 2);
        $odds['NUL'] = round(($nb_bets + 1) / ($odds['NUL'] + 1), 2);

        if (isset($prono) && ($prono['scoreMatchA'] !== NULL) && ($prono['scoreMatchB'] !== NULL)) {
            if ($nb_exact_bets == 0)
                $odds['NB_EXACT_BETS'] = "aucun score exact";
            elseif ($nb_exact_bets == 1)
                $odds['NB_EXACT_BETS'] = "1 score exact";
            else
                $odds['NB_EXACT_BETS'] = $nb_exact_bets . " scores exacts";
            $odds['NB_GOOD_BETS'] = $nb_good_bets . " bon(s) résultat(s)";
            if ($nb_good_bets == 0)
                $odds['NB_GOOD_BETS'] = "aucun bon résultat";
            elseif ($nb_good_bets == 1)
                $odds['NB_GOOD_BETS'] = "1 bon résultat";
            else
                $odds['NB_GOOD_BETS'] = $nb_good_bets . " bons résultats";
        }
        $odds['EXACT_BETS'] = $exact_bets;
        $odds['GOOD_BETS'] = $good_bets;

        if ($this->parent->debug) {
            array_show($odds);
        }

        return $odds;
    }

    function getByUser($userID, $phaseID=1, $mode=0) {
        // Main Query
        $req = "SELECT m.matchID, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, t1.teamID AS teamAid, t1.name AS teamAname, m.scoreA as scoreMatchA, m.scoreB as scoreMatchB, b.scoreA as scorePronoA, t2.teamID AS teamBid, t2.name AS teamBname, b.scoreB as scorePronoB, m.phaseID, m.status";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "pronos AS b ON((b.matchID = m.matchID) AND (b.userID = " . $userID . "))";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE m.phaseID=" . $phaseID;
        if ($mode == 1)
            $req .= " AND m.date < NOW()";
        $req .= " ORDER BY m.date";

        // Phase
        $phase = $this->parent->phases->getById($phaseID);
        //$phasePoints = $this->parent->settings->getPointsRules();
        $pronos = array();
        $pronosBdd = $this->parent->db->select_array($req, $nb_pronos);
        foreach ($pronosBdd as $prono) {
            // colors
            $classA = "tie";
            $classB = "tie";
            if ($prono['scorePronoA'] > $prono['scorePronoB']) {
                $classA = "win";
                $classB = "loose";
            }
            else if ($prono['scorePronoA'] < $prono['scorePronoB']) {
                $classA = "loose";
                $classB = "win";
            }
            $prono['CLASS_A'] = $classA;
            $prono['CLASS_B'] = $classB;
            $prono['SPECIAL'] = $prono['status'];

            //
            $resProno = $this->parent->settings->computeNbPtsProno($phase, $prono['status'], $prono['scoreMatchA'], $prono['scoreMatchB'], $prono['scorePronoA'], $prono['scorePronoB']);
            if ($resProno['points'] >= ($phase['nbPointsRes'] + $phase['nbPointsScore'] + $phase['nbPointsQualifie'])) {
                $color = "green";
                $points = "<strong>+" . $resProno['points'] . "pt";
                if($resProno['points'] > 1) {
                    $points .= "s";
                }
                $points .= "</strong>";
                
            } elseif ($resProno['points'] >= $phase['nbPointsRes']) {
                $color = "green";
                $points = "+" . $resProno['points'] . "pt";
                if($resProno['points'] > 1) {
                    $points .= "s";
                }
            } else {
                $color = "red";
                $points = $resProno['points'] . "pt";
            }
            $diff = "(" . $resProno['diff'] . ")";

            $prono['POINTS'] = 0;
            $prono['COLOR'] = "transparent";
            $prono['DIFF'] = 0;
            if (($prono['scoreMatchA'] !== NULL) && ($prono['scoreMatchB'] !== NULL)) {
                $prono['POINTS'] = $points;
                $prono['COLOR'] = $color;
                $prono['DIFF'] = $diff;
            }

            if ($phase['aller_retour'] == 1) {
                // match aller
                $matchAller = $this->getMatchByTeamsAndPhase($prono['teamBid'], $prono['teamAid'], $prono['phaseID']);
                if ($matchAller !== NULL) {
                    $prono['SCORE_ALLER_A'] = $matchAller['scoreA'];
                    $prono['SCORE_ALLER_B'] = $matchAller['scoreB'];
                    if (($prono['scorePronoA'] != $matchAller['scoreA']) || ($prono['scorePronoB'] != $matchAller['scoreB'])) {
                        $prono['pnyPronoA'] = NULL;
                        $prono['pnyPronoB'] = NULL;
                    }
                }
                if (strlen($prono['SCORE_ALLER_A']) == 0)
                    $prono['SCORE_ALLER_A'] = "-1";
                if (strlen($prono['SCORE_ALLER_B']) == 0)
                    $prono['SCORE_ALLER_B'] = "-1";
            }

            // limite de temps
            if (($prono['teamAid'] == NULL) || ($prono['teamBid'] == NULL))
                $disabled = ' disabled="disabled"';
            else {
                if ((($this->parent->games->getTimeBefore($prono['matchID']) < 900) || ($mode == 1)) && ($mode != 2))
                    $disabled = ' disabled="disabled"';
                else
                    $disabled = "";
            }
            $prono['DISABLED'] = $disabled;

            $pronos[] = $prono;
        }
        if ($this->parent->debug)
            array_show($pronos);

        return $pronos;
    }

    function getByUserTeamAndPhase($userID, $teamID, $phaseID=1) {
        // Main Query
        $req = "SELECT DISTINCT m.matchID, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, t1.teamID AS teamAid, t1.name AS teamAname, t2.teamID AS teamBid, t2.name AS teamBname";
        $req .= ", p.scoreA as scorePronoA, p.scoreB as scorePronoB";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "pronos p";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs AS m ON(m.matchID = p.matchID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "phases AS ph ON(m.phaseID = ph.phaseID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE ph.instanceID = " . $this->parent->config['current_instance'] . " AND p.userID = " . $userID . " AND (t1.teamID = " . $teamID . " OR t2.teamID = " . $teamID . ") AND m.phaseID = " . $phaseID;

        $pronos = $this->parent->db->select_array($req, $nb_pronos);
        if ($this->parent->debug)
            array_show($pronos);

        return $pronos;
    }

    function getByUserTeamUntilPhase($userID, $teamID, $phaseID=1) {
        // Main Query
        $req = "SELECT DISTINCT m.matchID, m.phaseID, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, t1.teamID AS teamAid, t1.name AS teamAname, t2.teamID AS teamBid, t2.name AS teamBname";
        $req .= ", p.scoreA as scorePronoA, p.scoreB as scorePronoB";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "phases AS ph ON(m.phaseID = ph.phaseID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "pronos AS p ON(m.matchID = p.matchID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE ph.instanceID = " . $this->parent->config['current_instance'] . " AND (p.userID = " . $userID . " OR p.userID IS NULL) AND (t1.teamID = " . $teamID . " OR t2.teamID = " . $teamID . ") AND m.phaseID <= " . $phaseID;
        
        $pronos = $this->parent->db->select_array($req, $nb_pronos);
        if ($this->parent->debug)
            array_show($pronos);

        return $pronos;
    }

    function getByUserAndPhase($userID, $phaseID=1) {
        // Main Query
        $req = "SELECT DISTINCT m.matchID, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, t1.teamID AS teamAid, t1.name AS teamAname, t2.teamID AS teamBid, t2.name AS teamBname";
        $req .= ", m.scoreA as scoreMatchA, m.scoreB as scoreMatchB";
        $req .= ", p.scoreA as scorePronoA, p.scoreB as scorePronoB";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "pronos p";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs AS m ON(m.matchID = p.matchID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE p.userID = " . $userID . " AND m.phaseID = " . $phaseID;

        $pronos = $this->parent->db->select_array($req, $nb_pronos);
        if ($this->parent->debug)
            array_show($pronos);

        return $pronos;
    }

    function getByUserUntilPhase($userID, $phaseID=1) {
        // Main Query
        $req = "SELECT DISTINCT m.matchID, m.phaseID, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, t1.teamID AS teamAid, t1.name AS teamAname, t2.teamID AS teamBid, t2.name AS teamBname";
        $req .= ", m.scoreA as scoreMatchA, m.scoreB as scoreMatchB";
        $req .= ", p.scoreA as scorePronoA, p.scoreB as scorePronoB";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "pronos p";
        $req .= " RIGHT JOIN " . $this->parent->config['db_prefix'] . "matchs AS m ON(m.matchID = p.matchID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "phases AS ph ON(m.phaseID = ph.phaseID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE ph.instanceID = " . $this->parent->config['current_instance'] . " AND (p.userID = " . $userID . " OR p.userID IS NULL) AND m.phaseID <= " . $phaseID;

        $pronos = $this->parent->db->select_array($req, $nb_pronos);
        if ($this->parent->debug)
            array_show($pronos);

        return $pronos;
    }

    function getLCPBetsByPhase($userID, $phaseID) {
        // Main Query
        $req = "SELECT p.userID, m.matchID, m.status";
        $req .= ", m.scoreA as scoreMatchA, m.scoreB as scoreMatchB";
        $req .= ", p.scoreA as scorePronoA, p.scoreB as scorePronoB";
        $req .= ", tA.teamID as teamAid, tB.teamID as teamBid, tA.name as teamAname, tB.name as teamBname";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m ";
        $req .= " RIGHT JOIN " . $this->parent->config['db_prefix'] . "pronos p ON (m.matchID = p.matchID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams tA ON (m.teamA = tA.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams tB ON (m.teamB = tB.teamID)";
        $req .= " WHERE m.phaseID = " . $phaseID . " AND m.status = 2 AND p.userID = " . $userID;

        $betsLCP = $this->parent->db->select_line($req, $nbBets);
        if ($this->parent->debug)
            array_show($betsLCP);

        return $betsLCP;
    }

    function getByGame($matchID) {
        // Main Query
        $req = "SELECT b.userID, m.matchID, m.status";
        $req .= ", m.scoreA as scoreMatchA, m.scoreB as scoreMatchB";
        $req .= ", b.scoreA as scorePronoA, b.scoreB as scorePronoB";
        $req .= ", tA.teamID as teamAid, tB.teamID as teamBid, tA.name as teamAname, tB.name as teamBname";
        $req .= ", DATE_FORMAT(date,'%a %e %M à %Hh%i') as dateStr";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m ";
        $req .= " RIGHT JOIN " . $this->parent->config['db_prefix'] . "pronos b ON (m.matchID = b.matchID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams tA ON (m.teamA = tA.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams tB ON (m.teamB = tB.teamID)";
        $req .= " WHERE m.matchID = " . $matchID;
        $req .= " ORDER BY m.date, teamAname";

        $pronosBdd = $this->parent->db->select_array($req, $nb_teams);
        $pronos = array();
        foreach ($pronosBdd as $prono) {
            // colors
            $classA = "tie";
            $classB = "tie";
            if ($prono['scorePronoA'] > $prono['scorePronoB']) {
                $classA = "win";
                $classB = "loose";
            }
            else if ($prono['scorePronoA'] < $prono['scorePronoB']) {
                $classA = "loose";
                $classB = "win";
            }
            $prono['CLASS_A'] = $classA;
            $prono['CLASS_B'] = $classB;

            // limite de temps
            if (($prono['teamAid'] == NULL) || ($prono['teamBid'] == NULL)) {
                $disabled = ' disabled="disabled"';
            }
            else {
                if ($this->parent->games->getTimeBefore($prono['matchID']) < 900) {
                    $disabled = ' disabled="disabled"';
                }
                else {
                    $disabled = "";
                }
            }
            $prono['DISABLED'] = $disabled;

            $pronos[] = $prono;
        }
        if ($this->parent->debug) {
            array_show($pronos);
        }

        return $pronos;
    }

    function getNumberOfByUser($userID) {
        // Main Query
        $req = "SELECT count(matchID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "pronos p";
        $req .= " WHERE p.userID = " . $userID;

        $nb_pronos = $this->parent->db->select_one($req);

        if ($this->parent->debug)
            echo($nb_pronos);

        return $nb_pronos;
    }

    function getNumberOfPlayedOnesByUser($userID) {
        // Main Query
        $req = "SELECT count(p.matchID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "pronos p";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs m ON(m.matchID = p.matchID)";
        $req .= " WHERE p.userID = " . $userID;
        $req .= " AND p.scoreA IS NOT NULL AND p.scoreB IS NOT NULL";
        $req .= " AND m.scoreA IS NOT NULL AND m.scoreB IS NOT NULL";

        $nb_pronos = $this->parent->db->select_one($req);

        if ($this->parent->debug)
            echo($nb_pronos);

        return $nb_pronos;
    }

    function getNumberOfPlayedOnesByUserAndPhase($userID, $phaseID) {
        // Main Query
        $req = "SELECT count(p.matchID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "pronos p";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs m ON (p.matchID = m.matchID)";
        $req .= " WHERE p.userID = " . $userID . " AND m.phaseID = " . $phaseID;
        $req .= " AND (p.scoreA IS NOT NULL) AND (p.scoreB IS NOT NULL)";

        $nb_pronos = $this->parent->db->select_one($req);

        if ($this->parent->debug)
            echo($nb_pronos);

        return $nb_pronos;
    }

    function getNumberOfPlayedOnesByGame($gameID) {
        // Main Query
        $req = "SELECT count(p.matchID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "pronos p";
        $req .= " WHERE p.matchID = " . $gameID;
        $req .= " AND (p.scoreA IS NOT NULL) AND (p.scoreB IS NOT NULL)";

        $nb_pronos = $this->parent->db->select_one($req);

        if ($this->parent->debug)
            echo($nb_pronos);

        return $nb_pronos;
    }
    
    function save($userID, $matchID, $team, $score, $isAdmin=0) {
        if ($score == "")
            $score = 'NULL';
        if ($this->isExists($userID, $matchID)) {
            $ret = $this->parent->db->exec_query("UPDATE " . $this->parent->config['db_prefix'] . "pronos SET score" . $team . " =" . addslashes($score) . " WHERE userID=" . $userID . " AND matchID=" . $matchID . "");
        } else {
            $ret = $this->parent->db->insert("INSERT INTO  " . $this->parent->config['db_prefix'] . "pronos (userID,matchID,score" . $team . ") VALUES ('" . addslashes($userID) . "','" . addslashes($matchID) . "'," . addslashes($score) . ")");
        }
        return $ret;
    }

    function isExists($userID, $matchID) {
        // Main Query
        $req = "SELECT matchID";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "pronos ";
        $req .= " WHERE userID = " . $userID;
        $req .= " AND matchID = " . $matchID;

        return $this->parent->db->select_one($req, null);
    }

    function getUsersMissingOnesByPhase($phaseID=false) {
        if ($phaseID) {
            // Main Query
            $req = "SELECT * FROM " . $this->parent->config['db_prefix'] . "users u";
            $req .= " WHERE u.userID NOT IN";
            $req .= " (SELECT userID FROM " . $this->parent->config['db_prefix'] . "pronos p LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs m ON(p.matchID = m.matchID) WHERE m.phaseID = " . $phaseID . ")";

            $users = $this->parent->db->select_array($req, $nb_users);
            if ($this->parent->debug)
                array_show($users);

            return $users;
        }
        else
            return array();
    }

    function getUsersMissingOnesByMatch($matchID=false) {
        if ($matchID) {
            // Main Query
            $req = "SELECT * FROM " . $this->parent->config['db_prefix'] . "users u";
            $req .= " WHERE u.userID NOT IN";
            $req .= " (SELECT userID FROM " . $this->parent->config['db_prefix'] . "pronos WHERE matchID = " . $matchID . ")";

            $users = $this->parent->db->select_array($req, $nb_users);
            if ($this->parent->debug)
                array_show($users);

            return $users;
        }
        else
            return array();
    }

}
