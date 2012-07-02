<?

class Phases {

    var $parent;

    function Phases(&$parent) {
        $this->parent = $parent;
    }

    function getPhaseIDActive() {
        // next phase within 3 days ?
        $req = "SELECT p.phaseID";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases AS p";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs AS m ON(p.phaseID = m.phaseID)";
        $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'];
        $req .= " AND m.scoreA IS NULL HAVING DATEDIFF(MIN(m.date), NOW()) <= 3";
        $phaseIDactive = $this->parent->db->select_one($req);
        
        // last played phase
        if(!$phaseIDactive) {
            $req = "SELECT p.phaseID, MAX(m.date) as mdate";
            $req .= " FROM " . $this->parent->config['db_prefix'] . "phases AS p";
            $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs AS m ON(p.phaseID = m.phaseID)";
            $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'];
            $req .= " AND m.scoreA is NOT NULL GROUP BY p.phaseID";
            $req .= " ORDER BY mdate DESC";
            $lastPhasesId = $this->parent->db->select_array($req, $nb_lines);
            if($lastPhasesId && (sizeof($lastPhasesId) > 0)) {
                $phaseIDactive = $lastPhasesId[0]['phaseID'];
            }
        }
        if(!$phaseIDactive) {
            $req = "SELECT p.phaseID";
            $req .= " FROM " . $this->parent->config['db_prefix'] . "phases AS p";
            $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'];
            $req .= " ORDER BY p.phaseID ASC";
            $phaseIDactive = $this->parent->db->select_one($req);
        }
        
        if($this->parent->debug) {
            echo "Phase active : " . $phaseIDactive . "<br />";
        }
        return $phaseIDactive;
    }

    function getNextPhaseIdToBet($instanceID = false) {
        $phaseIDactive = -1;
        $phaseIDjoue = -1;
        $phases = $this->get('ASC', $instanceID);
        foreach($phases as $phase) {
            $req = "SELECT count(*) FROM " . $this->parent->config['db_prefix'] . "matchs";
            $req .= " WHERE phaseID = " . $phase['phaseID'];
            $nb_matchs = $this->parent->db->select_one($req);
            
            $req = "SELECT count(*) FROM " . $this->parent->config['db_prefix'] . "matchs";
            $req .= " WHERE phaseID = " . $phase['phaseID'] . " AND scoreA IS NULL";
            $nb_matchs_non_joues = $this->parent->db->select_one($req);
            
            if($nb_matchs_non_joues > 0) {
                $phaseIDactive = $phase['phaseID'];
                break;
            }
            if(($nb_matchs > 0) && ($nb_matchs_non_joues == 0)) {
                $phaseIDjoue = $phase['phaseID'];
            }
        }
        if($phaseIDactive == -1) {
            $phaseIDactive = $phaseIDjoue;
        }
        if($this->parent->debug) {
            echo "Phase active : " . $phaseIDactive . "<br />";
        }
        return $phaseIDactive;
    }

    function isLast($phaseID) {
        $req = "SELECT count(*) FROM " . $this->parent->config['db_prefix'] . "phases";
        $req .= " WHERE phasePrecedente = " . $phaseID;
        $isNextPhase = $this->parent->db->select_one($req);
        return ($isNextPhase == 0);
    }

    function isFirst($phaseID) {
        $req = "SELECT count(*) FROM " . $this->parent->config['db_prefix'] . "phases";
        $req .= " WHERE phaseID = " . $phaseID;
        $req .= " AND phasePrecedente = NULL";
        $isNextPhase = $this->parent->db->select_one($req);
        return ($isNextPhase == 0);
    }

    function getById($id) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
        $req .= " WHERE p.phaseID = " . $id;

        $phase = $this->parent->db->select_line($req, $nb_lines);

        return $phase;
    }

    function getByName($name) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
        $req .= " WHERE p.name = " . $name;
        $req .= " AND p.instanceID = " . $this->parent->config['current_instance'];

        $phase = $this->parent->db->select_line($req, $nb_lines);

        return $phase;
    }

    function getFirstToFill() {
        // Main Query
        $req = "SELECT p.phaseID, p.name, p.phasePrecedente, p.nb_matchs, COUNT(m.matchID) AS nb_matchs_filled, p.nbPointsRes, p.nbPointsScore, p.multiplicateurMatchDuJour";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases AS p LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs AS m ON(p.phaseID = m.phaseID)";
        $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'];
        $req .= " GROUP BY p.phaseID HAVING nb_matchs_filled < 10";
        $req .= " ORDER BY p.phaseID ASC LIMIT 0,1";

        $phase = $this->parent->db->select_line($req, $nb_lines);

        return $phase;
    }

    function getPlayedOnes($order='DESC') {
        // Main Query
        $req = "SELECT p.phaseID, p.name, p.phasePrecedente, count(m.matchID) as nbMatchs, p.nbPointsRes, p.nbPointsScore, p.multiplicateurMatchDuJour";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs m ON(m.phaseID = p.phaseID)";
        $req .= " WHERE m.scoreA IS NOT NULL and scoreB IS NOT NULL";
        $req .= " AND p.instanceID = " . $this->parent->config['current_instance'];
        $req .= " GROUP BY (p.phaseID) HAVING nbMatchs > 0";
        $req .= " ORDER BY p.phaseID $order";

        $phases = $this->parent->db->select_array($req, $nb_phases);
        if($this->parent->debug)
            array_show($phases);

        return $phases;
    }

    function getPlayedOnesAndCurrent($order='DESC') {
        $phases = $this->getPlayedOnes($order);
        $phaseIdActive = $this->getPhaseIDActive();
        $isPhaseActiveInIt = false;
        foreach ($phases as $phase) {
            if($phase['phaseID'] == $phaseIdActive) {
                $isPhaseActiveInIt = true;
                break;
            }
        }
        if(!$isPhaseActiveInIt) {
            $phases[] = $this->getById($phaseIdActive);
        }

        if($this->parent->debug) {
            array_show($phases);
        }

        return $phases;
    }

    function getByRoot($id) {
        // Main Query
        $req = "SELECT *, count(m.matchID) as nbMatchs";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs m ON(m.phaseID = p.phaseID)";
        $req .= " WHERE ((p.phasePrecedente <= " . $id . ") OR (p.phasePrecedente IS NULL))";
        $req .= " AND p.instanceID = " . $this->parent->config['current_instance'];
        $req .= " GROUP BY (p.phaseID) HAVING nbMatchs > 0";
        $req .= " ORDER BY m.date DESC";

        $phases = $this->parent->db->select_array($req, $nb_phases);
        if($this->parent->debug) {
            array_show($phases);
        }

        return $phases;
    }

    function getByDirectRoot($id) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
        $req .= " WHERE p.phasePrecedente = " . $id;
 
        return $this->parent->db->select_line($req, $nb_phases);
    }

    function getFinalPlayedOnes() {
        // Main Query
        $req = "SELECT p.phaseID, p.name, count(m.matchID) as nbMatchs";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs m ON(m.phaseID = p.phaseID)";
        $req .= " WHERE p.phasePrecedente IS NOT NULL";
        $req .= " AND p.instanceID = " . $this->parent->config['current_instance'];
        $req .= " GROUP BY (p.phaseID) HAVING nbMatchs > 0";
        $req .= " ORDER BY p.phaseID DESC";

        $phases = $this->parent->db->select_array($req, $nb_phases);
        if($this->parent->debug)
            array_show($phases);

        return $phases;
    }

    function getFinalOnes() {
        // Main Query
        $req = "SELECT DISTINCT phasePrecedente";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
        $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'];
        $idsPre = $this->parent->db->select_col($req, $nb_cols);

        $finalPhases = array();
        $phases = $this->getPhases();
        foreach($phases as $phase) {
            if(!in_array($phase['phaseID'], $idsPre)) {
                $req = "SELECT *";
                $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
                $req .= " WHERE p.phaseID = " . $phase['phaseID'];
                $phase = $this->parent->db->select_array($req, $nb_phases);
                foreach($phase as $maPhase) {
                    $finalPhases[] = $maPhase;
                }
            }
        }

        return $finalPhases;
    }

    function get($order='ASC', $instanceID = false) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
        $req .= " WHERE p.instanceID = " . ( $instanceID ? $instanceID : $this->parent->config['current_instance']);
        $req .= " ORDER BY phaseID " . $order;

        $phases = $this->parent->db->select_array($req, $nb_teams);
        if($this->parent->debug) {
            array_show($phases);
        }

        return $phases;
    }

    function add($label) {
        // Get lat phase
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
        $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'];
        $req .= " ORDER BY phaseID DESC LIMIT 0,1";
        $phase = $this->parent->db->select_line($req, $nb_lines);

        // Add a new one
        $req = "INSERT INTO " . $this->parent->config['db_prefix'] . "phases (phaseID, instanceID, name, nb_matchs, nb_qualifies, phasePrecedente, nbPointsRes, nbPointsQualifie, nbPointsScore, multiplicateurMatchDuJour)";
        $req .= " VALUES (" . ($phase['phaseID'] + 1) . ", " . $this->parent->config['current_instance'] . ", '$label', " . $phase['nb_matchs'] . ", " . $phase['nb_qualifies'] . ", " . $phase['phaseID'] . ", ";
        $req .= $phase['nbPointsRes'] . ", " . $phase['nbPointsQualifie'] . ", " . $phase['nbPointsScore'] . ", " . $phase['multiplicateurMatchDuJour'] . ")";

        return $this->parent->db->insert($req);
    }

    function getMaxId() {
        // Get max id
        $req = "SELECT MAX(phaseID) as maxId";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "phases p";
        $req .= " WHERE p.instanceID = " . $this->parent->config['current_instance'];
        return $this->parent->db->select_one($req);
    }

    function move($phaseIdToMove, $phaseIdRef) {
        if($phaseIdToMove != $phaseIdRef) {
            $phaseToMove = $this->getById($phaseIdToMove);
            //echo "move phase " . $phaseIdToMove . " after phase " . $phaseIdRef . "<br/>";

            // move phases after phase ref
            $nextPhase = null;
            $currentRef = $phaseIdRef;
            $rootRef = $phaseIdRef + 1;

            // remove phase precedente for phase to move
            $req = "UPDATE " . $this->parent->config['db_prefix'] . "phases";
            $req .= " SET phasePrecedente = NULL";
            $req .= " WHERE phaseID = " . $phaseIdToMove;
            $this->parent->db->exec_query($req);
            
            // update phase after phase to move
            $phaseAfter = $this->getByDirectRoot($phaseIdToMove);
            if(isset($phaseAfter['phaseID'])) {
                $req = "UPDATE " . $this->parent->config['db_prefix'] . "phases";
                $req .= " SET phasePrecedente = " . (isset($phaseToMove['phasePrecedente'])?$phaseToMove['phasePrecedente']:"NULL");
                $req .= " WHERE phaseID = " . $phaseAfter['phaseID'];
                $this->parent->db->exec_query($req);
            }
            
            // move phases after ref
            do {
                $nextPhase = $this->getByDirectRoot($currentRef);
                if(isset($nextPhase['phaseID']) && ($nextPhase['phaseID'] == $phaseIdToMove)) {
                    $nextPhase = $this->getByDirectRoot($nextPhase['phaseID']);
                }
                if(isset($nextPhase['phaseID'])) {
                    //echo " -> phase to move " . $nextPhase['phaseID'] . "<br/>";

                    // update phase id and previous phase
                    $nextId = ($this->getMaxId() + 1);
                    //echo " -> phase to move : new id = " . $nextId . "<br/>";
                    $req = "UPDATE " . $this->parent->config['db_prefix'] . "phases";
                    $req .= " SET phaseID = " . $nextId;
                    $req .= ", phasePrecedente = " . $rootRef;
                    $req .= " WHERE phaseID = " . $nextPhase['phaseID'];
                    //echo "req to move phase : " . $req . "<br/>";
                    $this->parent->db->exec_query($req);

                    // update games
                    $req = "UPDATE " . $this->parent->config['db_prefix'] . "matchs SET phaseID = " . $nextId . " WHERE phaseID = " . $nextPhase['phaseID'];
                    //echo "req to update games : " . $req . "<br/>";
                    $this->parent->db->exec_query($req);

                    // prepare next phase
                    $currentRef = $nextPhase['phaseID'];
                    $rootRef = $nextId;
                }
                else {
                    $nextPhase = null;
                }
            } while($nextPhase != null);
            
            // update phase to move
            $req = "UPDATE " . $this->parent->config['db_prefix'] . "phases";
            $req .= " SET phaseID = " . ($phaseIdRef + 1);
            $req .= ", phasePrecedente = " . $phaseIdRef;
            $req .= " WHERE phaseID = " . $phaseIdToMove;
            $this->parent->db->exec_query($req);
            
            // update games of phase to move
            $req = "UPDATE " . $this->parent->config['db_prefix'] . "matchs SET phaseID = " . ($phaseIdRef + 1) . " WHERE phaseID = " . $phaseIdToMove;
            $this->parent->db->exec_query($req);
        }
    }
}
?>
