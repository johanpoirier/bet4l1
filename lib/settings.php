<?

class Settings {

    var $parent;

    function Settings(&$parent) {
        $this->parent = $parent;
    }

    function getLastGenerate() {
        // Main Query
        $req = "SELECT date";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "settings";
        $req .= " WHERE instanceID = " . $this->parent->config['current_instance'];
        $req .= " AND name = 'LAST_GENERATE'";

        $last_generate = $this->parent->db->select_one($req, null);

        if ($this->parent->debug)
            echo $last_generate;

        return $last_generate;
    }

    function getLastTeamGenerate() {
        // Main Query
        $req = "SELECT date";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "settings";
        $req .= " WHERE instanceID = " . $this->parent->config['current_instance'];
        $req .= " AND name = 'LAST_TEAM_GENERATE'";

        $last_team_generate = $this->parent->db->select_one($req, null);

        if ($this->parent->debug) {
            echo $last_team_generate;
        }

        return $last_team_generate;
    }

    function getSettingDate($name) {
        // Main Query
        $req = "SELECT date";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "settings";
        $req .= " WHERE instanceID = " . $this->parent->config['current_instance'];
        $req .= " AND name = '" . $name . "'";

        $myDate = $this->parent->db->select_one($req);
        if ($this->parent->debug)
            echo $myDate;

        return explode_datetime($myDate);
    }

    function getSettingValue($name) {
        // Main Query
        $req = "SELECT value";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "settings";
        $req .= " WHERE instanceID = " . $this->parent->config['current_instance'];
        $req .= " AND name = '" . $name . "'";

        $myValue = $this->parent->db->select_one($req);
        if ($this->parent->debug)
            echo $myValue;

        return $myValue;
    }

    function getMonths() {
        global $lang;

        // Main Query
        $req = "SELECT date, DATE_FORMAT(date, '%m') as month";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "settings";
        $req .= " WHERE instanceID = " . $this->parent->config['current_instance'];
        $req .= " AND (name = 'DATE_DEBUT' OR name = 'DATE_FIN')";
        $req .= " ORDER BY date";

        $months = array();
        $dates = $this->parent->db->select_array($req, $nb_res);
        $m = 0;
        $debMonth = 0;
        $finMonth = 0;
        if (sizeof($dates) > 0) {
            $debMonth = intval($dates[0]['month']);
            if (sizeof($dates) > 1) {
                $finMonth = intval($dates[1]['month']);
            }
            else
                $finMonth = $debMonth;
        }
        $yearModificator = 0;
        if ($finMonth < $debMonth) {
            $yearModificator = 1;
        }

        $j = 0;
        for ($m = $debMonth; $m <= ($finMonth + $yearModificator * 12); $m++) {
            $realMonth = $m;
            if ($m > 12)
                $realMonth = $m - $yearModificator * 12;
            $months[$j++] = array($realMonth, $lang['months'][$realMonth - 1]);
        }

        return $months;
    }

    function getYears() {
        // Main Query
        $req = "SELECT date, DATE_FORMAT(date, '%Y') as year";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "settings";
        $req .= " WHERE instanceID = " . $this->parent->config['current_instance'];
        $req .= " AND (name = 'DATE_DEBUT' OR name = 'DATE_FIN')";
        $req .= " ORDER BY date";

        $yearsInDB = $this->parent->db->select_array($req, $nb_res);
        $years = array();
        foreach ($yearsInDB as $year) {
            $years[] = $year['year'];
        }

        return $years;
    }

    function getPointsRules() {
        // Main Query
        $req = "SELECT phaseID, nbPointsRes, nbPointsScore, multiplicateurMatchDuJour";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases";
        $req .= " WHERE instanceID = " . $this->parent->config['current_instance'];
        $req .= " ORDER BY phaseID ASC";

        $phases = $this->parent->db->select_array($req, $nb_teams);
        $rules = array();
        foreach ($phases as $phase) {
            $rules[$phase['phaseID']] = array($phase['nbPointsRes'], $phase['nbPointsScore'], $phase['multiplicateurMatchDuJour']);
        }
        if ($this->parent->debug)
            array_show($rules);

        return $rules;
    }

    function setLastGenerate($lcp = false) {
        $setting_name = "LAST_GENERATE";
        if($lcp) {
            $setting_name .= "_LCP";
        }

        // Main Query
        $req = "REPLACE";
        $req .= " INTO " . $this->parent->config['db_prefix'] . "settings";
        $req .= " (instanceID, name, date)";
        $req .= " VALUES (" . $this->parent->config['current_instance'] . ", '" . $setting_name . "', NOW())";

        $this->parent->db->exec_query($req);

        return;
    }

    function isRankToUpdate($lcp = false) {
        $setting_name = "LAST_GENERATE";
        if($lcp) {
            $setting_name .= "_LCP";
        }
        
        $req = "SELECT 1";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "settings";
        $req .= " WHERE name = '" . $setting_name . "'";
        $req .= " AND instanceID = " . $this->parent->config['current_instance'];
        $req .= " AND DATE_FORMAT(date, '%m%e') <> DATE_FORMAT(NOW(), '%m%e')";
        $isLastGenerate = $this->parent->db->select_one($req, null);

        if ($isLastGenerate == 1) {
            $req = "SELECT count(m.matchID) as nbMatchs";
            $req .= " FROM " . $this->parent->config['db_prefix'] . "matchs as m";
            $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "phases as p ON (m.phaseID = p.phaseID)";
            $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'];
            $req .= " AND DATE_FORMAT(m.date, '%m%e') = DATE_FORMAT(NOW(), '%m%e')";
            $req .= " AND m.scoreA IS NULL AND m.scoreB IS NULL";
            $nbMacths = $this->parent->db->select_one($req, null);
            return ($nbMacths == 0);
        }
        else {
            return false;
        }
    }

    function computeNbPtsProno($phase, $isSpecial, $scoreMatchA, $scoreMatchB, $scorePronoA, $scorePronoB) {
        $nbPoints = 0;
        $resJuste = 0;
        $scoreJuste = 0;
        $bonus = 0;
        $diff = 0;

        $winnerMatch = 'x';
        $winnerProno = 'y';
        $resMatch = 'x';
        $resProno = 'y';

        // Real winner
        if (($scoreMatchA != NULL) && ($scoreMatchB != NULL)) {
            if ($scoreMatchA > $scoreMatchB) {
                $winnerMatch = 'A';
                $resMatch = 'A';
            } elseif ($scoreMatchA < $scoreMatchB) {
                $winnerMatch = 'B';
                $resMatch = 'B';
            } else {
                $resMatch = 'N';
            }
        }

        // Prono winner
        if (($scorePronoA != NULL) && ($scorePronoB != NULL)) {
            if ($scorePronoA > $scorePronoB) {
                $winnerProno = 'A';
                $resProno = 'A';
            } elseif ($scorePronoA < $scorePronoB) {
                $winnerProno = 'B';
                $resProno = 'B';
            } else {
                $resProno = 'N';
            }
        }

        // Computing points
        if ($resProno == $resMatch) {
            // Result
            $nbPoints += $phase['nbPointsRes'];
            $resJuste = 1;

            if (($scoreMatchA == $scorePronoA) && ($scoreMatchB == $scorePronoB)) {
                $nbPoints += $phase['nbPointsScore'];
                $scoreJuste = 1;
            }

            if ($isSpecial == 1) {
                $nbPointsAvant = $nbPoints;
                $nbPoints = $nbPoints * $phase['multiplicateurMatchDuJour'];
                $bonus = $nbPoints - $nbPointsAvant;
            }
        }

        // Difference de buts avec le score reel
        $diff -= abs($scoreMatchA - $scorePronoA) + abs($scoreMatchB - $scorePronoB);

        $retour = array("points" => $nbPoints, "res" => $resJuste, "score" => $scoreJuste, "bonus" => $bonus, "diff" => $diff);

        if ($this->parent->debug)
            array_show($retour);

        return $retour;
    }
}
?>
