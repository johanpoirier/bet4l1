<?php

class Games {

    var $parent;

    function Games(&$parent) {
        $this->parent = $parent;
    }

    function add($phase, $day, $month, $year, $hour, $minutes, $teamA, $teamB, $isSpecial, $idMatch) {
        $date = $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minutes . ":00";
        if ($idMatch != null) {
            return $this->parent->db->exec_query("UPDATE " . $this->parent->config['db_prefix'] . "matchs SET date = '" . addslashes($date) . "', teamA = $teamA, teamB = $teamB, phaseID = $phase, status = $isSpecial WHERE matchID = " . $idMatch . "");
        } else {
            return $this->parent->db->insert("INSERT INTO  " . $this->parent->config['db_prefix'] . "matchs (date, teamA, teamB, phaseID, status) VALUES ('" . addslashes($date) . "', '" . addslashes($teamA) . "', '" . addslashes($teamB) . "', " . addslashes($phase) . ", $isSpecial)");
        }
    }

    function delete($matchID) {
        // Main Query
        $req = "DELETE";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs";
        $req .= " WHERE matchID=" . $matchID;

        $this->parent->db->exec_query($req);

        return;
    }

    function getNumberOf() {
        // Main Query
        $req = "SELECT count(matchID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";

        $nb_matchs = $this->parent->db->select_one($req);

        if ($this->parent->debug)
            echo($nb_matchs);

        return $nb_matchs;
    }

    function get() {
        // Main Query
        $req = "SELECT m.matchID, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, tA.teamID as teamAid, tB.teamID as teamBid, tA.name as teamAname, tB.name as teamBname, m.phaseID";
        $req .= ", m.scoreA as scoreMatchA, m.scoreB as scoreMatchB";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m ";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams tA ON (m.teamA = tA.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams tB ON (m.teamB = tB.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "phases p ON (m.phaseID = p.phaseID)";
        $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'];
        $req .= " ORDER BY m.date";

        $matchs = array();
        $matchsBdd = $this->parent->db->select_array($req, $nb_teams);
        foreach ($matchsBdd as $match) {
            $colorA = "transparent";
            $colorB = "transparent";
            if ($match['scoreMatchA'] > $match['scoreMatchB'])
                $colorA = "#99FF99";
            else if ($match['scoreMatchA'] < $match['scoreMatchB'])
                $colorB = "#99FF99";
            $match['COLOR_A'] = $colorA;
            $match['COLOR_B'] = $colorB;
            $matchs[] = $match;
        }
        if ($this->parent->debug)
            array_show($matchs);

        return $matchs;
    }

    function getNbMatchsFromPhaseInNextDays($phaseID, $days=2) {
        // Main Query
        $req = "SELECT COUNT(*) AS nb_matchs FROM " . $this->parent->config['db_prefix'] . "matchs";
        $req .= " WHERE phaseID = " . $phaseID . " AND DATEDIFF(date, NOW()) <= " . $days;

        $nbMatchs = $this->parent->db->select_one($req, null);
        if ($this->parent->debug)
            echo("nb matchs : " . $nbMatchs);

        return $nbMatchs;
    }

    function getUntilPhase($phaseID) {
        // Main Query
        $req = "SELECT m.matchID, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, t1.teamID AS teamAid, t1.name AS teamAname, t2.teamID AS teamBid, t2.name AS teamBname, m.phaseID";
        $req .= ", m.scoreA as scoreMatchA, m.scoreB as scoreMatchB";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "phases AS p ON(m.phaseID = p.phaseID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'] . " AND m.phaseID <= " . $phaseID; // ----> trop pourri, à changer absolument...
        $req .= " ORDER BY m.date ASC";

        $matchs = $this->parent->db->select_array($req, $nb_teams);
        if ($this->parent->debug)
            array_show($matchs);

        return $matchs;
    }

    function getByTeamAndPhase($teamID, $phaseID=1) {
        // Main Query
        $req = "SELECT m.matchID, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, t1.teamID AS teamAid, t1.name AS teamAname, t2.teamID AS teamBid, t2.name AS teamBname";
        $req .= ", m.scoreA as scoreMatchA, m.scoreB as scoreMatchB";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "phases AS p ON(m.phaseID = p.phaseID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'] . " AND (t1.teamID = " . $teamID . " OR t2.teamID = " . $teamID . ") AND m.phaseID = " . $phaseID;

        $matchs = $this->parent->db->select_array($req, $nb_teams);
        if ($this->parent->debug)
            array_show($matchs);

        return $matchs;
    }

    function getByPhase($phaseID) {
        // Main Query
        $req = "SELECT m.matchID, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, t1.teamID AS teamAid, t1.name AS teamAname, t2.teamID AS teamBid, t2.name AS teamBname, m.status";
        $req .= ", m.scoreA as scoreMatchA, m.scoreB as scoreMatchB";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE phaseID=" . $phaseID;
        $req .= " ORDER BY m.date ASC";

        $matchs = $this->parent->db->select_array($req, $nb_teams);
        if ($this->parent->debug)
            array_show($matchs);

        return $matchs;
    }

    function getById($matchID) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " WHERE matchID = $matchID";

        $match = $this->parent->db->select_line($req, $nb_teams);

        return $match;
    }

    function getNextOnes() {
        // Main Query
        $req = "SELECT *, tA.teamID as teamAid, tB.teamID as teamBid, tA.name as teamAname, tB.name as teamBname, DATE_FORMAT(date, 'le %e/%m à %Hh') as date_str, TIME_TO_SEC(TIMEDIFF(date, NOW())) as delay_sec";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m ";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "phases p ON (m.phaseID = p.phaseID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams tA ON (m.teamA = tA.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams tB ON (m.teamB = tB.teamID)";
        $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'] . " AND date = (";
        $req .= " SELECT MIN(date) ";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m ";
        $req .= " WHERE date > NOW()";
        $req .= ") LIMIT 0,3";

        $matchs = $this->parent->db->select_array($req, $nb_matchs);

        if ($this->parent->debug)
            array_show($matchs);

        return $matchs;
    }

    function getNbMatchsInTheNextNDays($nbDays) {
        // Main Query
        $req = "SELECT count(m.matchID) FROM l1__matchs AS m";
        $req .= " WHERE DATEDIFF(m.date, NOW()) >= 0 AND DATEDIFF(m.date, NOW()) <= " . $nbDays;

        $nbMatchs = $this->parent->db->select_one($req);
        
        return $nbMatchs;
    }

    function getByTeamsAndPhase($teamAid, $teamBid, $phase) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " WHERE teamA = $teamAid AND teamB = $teamBid AND phaseID = $phase";

        $match = $this->parent->db->select_line($req, $nb_teams);

        return $match;
    }

    function resetNbMatchsPlayed() {
        // Main Query
        $req = "UPDATE " . $this->parent->config['db_prefix'] . "settings";
        $req .= " SET value = 0";
        $req .= " WHERE name = 'NB_MATCHS_PLAYED'";

        $this->parent->db->exec_query($req);

        return;
    }

    function isExists($teamA, $teamB, $phaseID) {
        // Main Query
        $req = "SELECT matchID";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m ";
        $req .= " WHERE teamA = " . $teamA;
        $req .= " AND teamB = " . $teamB;
        $req .= " AND phaseID = " . $phaseID;

        return $this->parent->db->select_one($req, null);
    }

    function getNbMatchsPlayedByPhase($phaseID) {
        // Main Query
        $req = "SELECT count(DISTINCT m.matchID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " WHERE m.phaseID = " . $phaseID;
        $req .= " AND m.scoreA IS NOT NULL AND scoreB IS NOT NULL";

        $nb_matchs = $this->parent->db->select_one($req);

        return $nb_matchs;
    }

    function getNbMatchsByPhase($phaseID) {
        // Main Query
        $req = "SELECT count(DISTINCT m.matchID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " WHERE m.phaseID = " . $phaseID;

        $nb_matchs = $this->parent->db->select_one($req);

        return $nb_matchs;
    }

    function getNumberOfPlayedOnes() {
        // Main Query
        $req = "SELECT count(DISTINCT m.matchID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " WHERE m.scoreA IS NOT NULL AND scoreB IS NOT NULL";

        $nb_matchs = $this->parent->db->select_one($req);

        return $nb_matchs;
    }

    function getTimeBefore($matchID) {
        // Main Query
        $req = "SELECT TIME_TO_SEC(TIMEDIFF(date, NOW())) as delay_sec";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m ";
        $req .= " WHERE matchID=" . $matchID;
        $res = $this->parent->db->select_one($req);

        return $res;
    }

    function getResultsByPhase($phase = 1) {
        // Main Query
        $req = "SELECT m.matchID, m.status, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, t1.teamID AS teamAid, t1.name AS teamAname, m.scoreA as scoreMatchA, m.scoreB as scoreMatchB, t2.teamID AS teamBid, t2.name AS teamBname";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE m.phaseID=" . $phase;
        $req .= " ORDER BY m.date";

        $nb_teams = 0;
        $results = [];
        $resultsBdd = $this->parent->db->select_array($req, $nb_teams);
        foreach ($resultsBdd as $result) {
            // colors
            $classA = "tie";
            $classB = "tie";
            if ($result['scoreMatchA'] > $result['scoreMatchB']) {
                $classA = "win";
                $classB = "loose";
            }
            else if ($result['scoreMatchA'] < $result['scoreMatchB']) {
                $classA = "loose";
                $classB = "win";
            }
            $result['CLASS_A'] = $classA;
            $result['CLASS_B'] = $classB;
            $result['SPECIAL'] = $result['status'];

            $results[] = $result;
        }
        if ($this->parent->debug)
            array_show($results);

        return $results;
    }

    function saveResult($matchID, $team, $score) {
        if ($score == "") {
            $score = 'NULL';
        }
        $req = "UPDATE " . $this->parent->config['db_prefix'] . "matchs";
        $req .= " SET score" . $team . " =" . addslashes($score);
        $req .= " WHERE matchID=" . $matchID;

        $ret = $this->parent->db->exec_query($req);

        return $ret;
    }

    function isDatePassed($matchID) {
        // Main Query
        $req = "SELECT 1";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m ";
        $req .= " WHERE matchID = " . $matchID;
        $req .= " AND NOW() > m.date";

        $res = $this->parent->db->select_one($req, null);
        return $res ? 1 : 0;
    }

    function getByTeamRssNames($instanceID, $teamAName, $teamBName) {
        // Main Query
        $req = "SELECT m.matchID, m.status, DATE_FORMAT(m.date,'%a %e %M à %Hh%i') as dateStr, t1.teamID AS teamAid, t1.name AS teamAname, m.scoreA as scoreMatchA, m.scoreB as scoreMatchB, t2.teamID AS teamBid, t2.name AS teamBname";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m ";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "phases AS p ON(m.phaseID = p.phaseID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t1 ON(m.teamA = t1.teamID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t2 ON(m.teamB = t2.teamID)";
        $req .= " WHERE p.instanceID = " . $instanceID;
        $req .= " AND t1.rssName = '" . $teamAName . "'";
        $req .= " AND t2.rssName = '" . $teamBName . "'";

        $match = $this->parent->db->select_line($req, $nb_teams);

        return $match;
    }
}
