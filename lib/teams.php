<?php

class Teams {

    var $parent;
    var $max_results = 1000;

    function Teams(&$parent) {
        $this->parent = $parent;
    }

    function getRanking($teams, $pronos, $libScore, $userID=false) {
        $nbPtsVictoire = $this->parent->settings->getSettingValue("NB_POINTS_VICTOIRE");
        $nbPtsNul = $this->parent->settings->getSettingValue("NB_POINTS_NUL");

        $array_teams = array();
        foreach($teams as $team) {
            $team['points'] = 0;
            $team['diff'] = 0;
            if($libScore == 'scoreMatch')
                $team['matchs'] = $this->parent->games->getByTeamAndPhase($team['teamID'], 1);
            else
                $team['matchs'] = $this->parent->bets->getByUserTeamAndPhase($_SESSION['userID'], $team['teamID'], 1);
            $array_teams[$team['teamID']] = $team;
        }

        foreach($pronos as $prono) {
            if(!isset($array_teams[$prono['teamAid']]['gf']))
                $array_teams[$prono['teamAid']]['gf'] = 0;
            $array_teams[$prono['teamAid']]['gf'] += $prono[$libScore . 'A'];
            if(!isset($array_teams[$prono['teamBid']]['gf']))
                $array_teams[$prono['teamBid']]['gf'] = 0;
            $array_teams[$prono['teamBid']]['gf'] += $prono[$libScore . 'B'];

            if($prono[$libScore . 'A'] > $prono[$libScore . 'B']) {
                $array_teams[$prono['teamAid']]['points'] += $nbPtsVictoire;
                $array_teams[$prono['teamAid']]['diff'] += ( $prono[$libScore . 'A'] - $prono[$libScore . 'B']);
                $array_teams[$prono['teamBid']]['diff'] -= ( $prono[$libScore . 'A'] - $prono[$libScore . 'B']);
            }
            if($prono[$libScore . 'A'] < $prono[$libScore . 'B']) {
                $array_teams[$prono['teamBid']]['points'] += $nbPtsVictoire;
                $array_teams[$prono['teamAid']]['diff'] -= ( $prono[$libScore . 'B'] - $prono[$libScore . 'A']);
                $array_teams[$prono['teamBid']]['diff'] += ( $prono[$libScore . 'B'] - $prono[$libScore . 'A']);
            }
            if($prono[$libScore . 'A'] == $prono[$libScore . 'B'] && ($prono[$libScore . 'A'] != "")) {
                $array_teams[$prono['teamAid']]['points'] += $nbPtsNul;
                $array_teams[$prono['teamBid']]['points'] += $nbPtsNul;
            }
        }

        if($libScore == 'scoreMatch') {
            usort($array_teams, "compare_teams_1to1");
        } else {
            usort($array_teams, "compare_pronoteams_1to1");
        }

        // Coloration des qualifies
        for($i = 0; $i < 2; $i++) {
            $array_teams[$i]['class'] = "first";
        }
        for($i = sizeof($array_teams) - 1; $i > (sizeof($array_teams) - 4); $i--) {
            $array_teams[$i]['class'] = "last";
        }

        return $array_teams;
    }

    function getMixedRanking($teams, $pronos, $phaseID, $userID) {
        $nbPtsVictoire = $this->parent->settings->getSettingValue("NB_POINTS_VICTOIRE");
        $nbPtsNul = $this->parent->settings->getSettingValue("NB_POINTS_NUL");

        $array_teams = array();
        foreach($teams as $team) {
            $team['points'] = 0;
            $team['diff'] = 0;
            $team['matchs'] = $this->parent->bets->getByUserTeamUntilPhase($userID, $team['teamID'], 1);
            $array_teams[$team['teamID']] = $team;
        }

        foreach($pronos as $prono) {
            //echo "$phaseID vs ".$prono['phaseID']."<br/>";
            if($prono['phaseID'] == $phaseID)
                $libScore = 'scoreProno';
            else
                $libScore = 'scoreMatch';

            if(!isset($array_teams[$prono['teamAid']]['gf']))
                $array_teams[$prono['teamAid']]['gf'] = 0;
            $array_teams[$prono['teamAid']]['gf'] += $prono[$libScore . 'A'];
            if(!isset($array_teams[$prono['teamBid']]['gf']))
                $array_teams[$prono['teamBid']]['gf'] = 0;
            $array_teams[$prono['teamBid']]['gf'] += $prono[$libScore . 'B'];

            if($prono[$libScore . 'A'] > $prono[$libScore . 'B']) {
                $array_teams[$prono['teamAid']]['points'] += $nbPtsVictoire;
                $array_teams[$prono['teamAid']]['diff'] += ( $prono[$libScore . 'A'] - $prono[$libScore . 'B']);
                $array_teams[$prono['teamBid']]['diff'] -= ( $prono[$libScore . 'A'] - $prono[$libScore . 'B']);
            }
            if($prono[$libScore . 'A'] < $prono[$libScore . 'B']) {
                $array_teams[$prono['teamBid']]['points'] += $nbPtsVictoire;
                $array_teams[$prono['teamAid']]['diff'] -= ( $prono[$libScore . 'B'] - $prono[$libScore . 'A']);
                $array_teams[$prono['teamBid']]['diff'] += ( $prono[$libScore . 'B'] - $prono[$libScore . 'A']);
            }
            if($prono[$libScore . 'A'] == $prono[$libScore . 'B'] && ($prono[$libScore . 'A'] != "")) {
                $array_teams[$prono['teamAid']]['points'] += $nbPtsNul;
                $array_teams[$prono['teamBid']]['points'] += $nbPtsNul;
            }
        }

        usort($array_teams, "compare_pronoteams_1to1");

        // Coloration des qualifies
        for($i = 0; $i < 2; $i++) {
            $array_teams[$i]['class'] = "first";
        }
        for($i = sizeof($array_teams) - 1; $i > (sizeof($array_teams) - 4); $i--) {
            $array_teams[$i]['class'] = "last";
        }
        
        return $array_teams;
    }

    function getQualifiedTeamsByPhase($phase, $type='Match', $userID=0) {
        $teamsQualified = array();
        if($phase['phasePrecedente'] != NULL) {
            $phasePrecedente = $this->phases->getPhase($phase['phasePrecedente']);
            $indic = $phase['nb_qualifies'];
            if($indic > 0)
                $indic = 1;
            if($type == 'Match')
                $matchs = $this->getMatchsByPhase($phasePrecedente['phaseID']);
            elseif($type == 'Prono')
                $matchs = $this->bets->getPronosByUserAndPhase($userID, $phasePrecedente['phaseID']);
            else
                $matchs = array();
            foreach($matchs as $match) {
                $idTeam = 0;
                if(($indic * $match['score' . $type . 'A']) > ($indic * $match['score' . $type . 'B']))
                    $idTeam = $match['teamAid'];
                elseif(($indic * $match['score' . $type . 'A']) < ($indic * $match['score' . $type . 'B']))
                    $idTeam = $match['teamBid'];
                else {
                    if(($indic * $match['pny' . $type . 'A']) > ($indic * $match['pny' . $type . 'B']))
                        $idTeam = $match['teamAid'];
                    else
                        $idTeam = $match['teamBid'];
                }
                $teamsQualified[] = $this->getTeam($idTeam);
            }
        }
        else {
            $teamsQualified = $this->getTeamsByPhase($phase['phaseID']);
        }
        return $teamsQualified;
    }

    function get($instanceId = false) {
        if(!$instanceId) {
            $instanceId = $this->parent->config['current_instance'];
        }

        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "teams";
        $req .= " WHERE instanceID = " . $instanceId;
        $req .= " ORDER BY name ASC";

        $teams = $this->parent->db->select_array($req, $this->max_results);
        if($this->parent->debug) {
            array_show($teams);
        }

        return $teams;
    }

    function getByPhase($phaseID) {
        $req = "SELECT DISTINCT t.teamID, t.name";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs m";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "teams AS t ON(m.teamA = t.teamID)";
        $req .= " WHERE m.phaseID = " . $phaseID;

        $teams = $this->parent->db->select_array($req, $this->max_results);
        if($this->parent->debug)
            array_show($teams);

        return $teams;
    }

    function getById($teamID) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "teams t";
        $req .= " WHERE t.teamID = " . $teamID;

        $team = $this->parent->db->select_line($req, $this->max_results);
        if($this->parent->debug)
            array_show($team);

        return $team;
    }

    function add($name, $rssName, $instanceId = false) {
        if(!$instanceId) {
            $instanceId = $this->parent->config['current_instance'];
        }

        $rssName = trim($rssName);
        $name = trim($name);
        if ($name == null || $name == "") {
            return FIELDS_EMPTY;
        }

        $req = "INSERT INTO " . $this->parent->config['db_prefix'] . "teams (instanceID, name, rssName)";
        $req .= " VALUES (" . $instanceId . ", '" . addslashes($name) . "', '" . $rssName . "')";

        return $this->parent->db->insert($req);
    }
}
