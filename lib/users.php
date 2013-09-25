<?

class Users {

    var $parent;
    var $max_size = 10000;

    function Users(&$parent) {
        $this->parent = $parent;
    }

    function isExists($login, $instanceId = false) {
        if(!$instanceId) {
            $instanceId = $this->parent->config['current_instance'];
        }

        $req = "SELECT userID";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "users ";
        $req .= " WHERE LOWER(login) = '" . strtolower($login) . "'";
        $req .= " AND instanceID = " . $instanceId;

        return $this->parent->db->select_one($req, null);
    }

    function updateProfile($userID, $name, $email, $pwd, $email_preferences) {
        $req = "UPDATE " . $this->parent->config['db_prefix'] . "users SET";
        if (strlen($email_preferences) == 2) {
            $req .= " email_preferences = '" . $email_preferences . "'";
        }
        if (strlen($name) > 3) {
            $req .= ", name = '" . addslashes($name) . "'";
        }
        if (strlen($email) > 5) {
            $req .= ", email = '" . addslashes($email) . "'";
        }
        if (strlen($pwd) > 2) {
            $req .= ", password = '" . md5($pwd) . "'";
        }
        $req .= " WHERE userID=" . $userID;
        $ret = $this->parent->db->exec_query($req);

        return $ret;
    }

    function addTeam($name, $instanceId = false) {
        if(!$instanceId) {
            $instanceId = $this->parent->config['current_instance'];
        }
        return $this->parent->db->insert("INSERT INTO  " . $this->parent->config['db_prefix'] . "user_teams (instanceID, name, lastRank) VALUES (" . $instanceId . ", '" . addslashes($name) . "', 1)");
    }

    function add($login, $pass, $name, $firstname, $email, $groupID, $status, $instanceId = false, $passEncrypted = false) {
        $login = trim($login);
        $email = trim($email);
        $name = trim($name);
        $firstname = trim($firstname);

        if(!$instanceId) {
            $instanceId = $this->parent->config['current_instance'];
        }
        if (strlen($firstname) > 0) {
            $name = $firstname . " " . $name;
        }
        if (!stristr($email, '@')) {
            echo "INCORRECT_EMAIL : " . $email;
            return INCORRECT_EMAIL;
        }
        if ($this->isExists($login, $instanceId)) {
            return LOGIN_ALREADY_EXISTS;
        }
        if ($name == null || $name == "" || $login == null || $login == "") {
            return FIELDS_EMPTY;
        }
        if(!$passEncrypted) {
            $pass = md5($pass);
        }

        $req = "INSERT INTO " . $this->parent->config['db_prefix'] . "users (login, password, name, email, userTeamID, status, instanceID)";
        $req .= " VALUES ('" . addslashes($login) . "', '" . $pass . "', '" . addslashes($name) . "', '" . addslashes($email) . "', " . (($groupID != '') ? $groupID : "NULL") . ", " . addslashes($status) . ", " . $instanceId . ")";

        return $this->parent->db->insert($req);
    }

    function addOrUpdate($login, $pass, $name, $email, $groupID, $status) {
        $login = trim($login);
        $name = trim($name);
        $email = trim($email);
        if (!stristr($email, '@')) {
            return INCORRECT_EMAIL;
        }
        if ($name == null || $name == "" || $login == null || $login == "") {
            return FIELDS_EMPTY;
        }
        if ($this->isExists($login)) {
            $passwordReq = "";
            if (strlen($pass) > 1) {
                $passwordReq = " password='" . md5($pass) . "', ";
            }
            $req = "UPDATE " . $this->parent->config['db_prefix'] . "users";
            $req .= " SET name='" . addslashes($name) . "', " . $passwordReq . "email='" . addslashes($email) . "',";
            $req .= " userTeamID=" . (($groupID != '') ? $groupID : "NULL") . ", status=" . addslashes($status) . " WHERE login='" . addslashes($login) . "'";
            return $this->parent->db->exec_query($req);
        } else {
            $req = "INSERT INTO " . $this->parent->config['db_prefix'] . "users (login, password, name, email, userTeamID, status, instanceID)";
            $req .= " VALUES ('" . addslashes($login) . "', '" . md5($pass) . "', '" . addslashes($name) . "', '" . addslashes($email) . "', " . (($groupID != '') ? $groupID : "NULL") . ", " . addslashes($status) . ", " . $this->parent->config['current_instance'] . ")";
            return $this->parent->db->insert($req);
        }
    }

    function delete($login) {
        // Main Query
        $req = "DELETE";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "users";
        $req .= " WHERE login='" . addslashes($login) . "'";
        $req .= " AND instanceID = " . $this->parent->config['current_instance'];

        $this->parent->db->exec_query($req);

        return;
    }

    function getRanks() {
        $users = $this->get();
        $ranks = array();
        usort($users, "compare_users");
        $i = 1;
        $j = 0;
        $last_user = $users[0];
        foreach ($users as $ID => $user) {
            if ($user['nbpronos'] == 0) {
                $ranks[$user['userID']] = 'NULL';
                continue;
            }
            if (compare_users($user, $last_user) != 0)
                $i = $j + 1;
            $ranks[$user['userID']] = $i;
            $j++;
            $last_user = $user;
        }

        return $ranks;
    }

    function getRanksLCP() {
        $users = $this->get();
        $ranks = array();
        usort($users, "compare_users_lcp");
        $i = 1;
        $j = 0;
        $last_user = $users[0];
        foreach ($users as $ID => $user) {
            if ($user['nbpronos'] == 0) {
                $ranks[$user['userID']] = 'NULL';
                continue;
            }
            if (compare_users_lcp($user, $last_user) != 0)
                $i = $j + 1;
            $ranks[$user['userID']] = $i;
            $j++;
            $last_user = $user;
        }

        return $ranks;
    }

    function getTeamsRank() {
        $userTeams = $this->getTeams();
        $teamRanks = array();
        usort($userTeams, "compare_user_teams");
        $i = 1;
        $j = 0;
        $lastUserTeam = $userTeams[0];
        foreach ($userTeams as $ID => $userTeam) {
            if (compare_user_teams($userTeam, $lastUserTeam) != 0)
                $i = $j + 1;
            $teamRanks[$userTeam['userTeamID']] = $i;
            $j++;
            $lastUserTeam = $userTeam;
        }

        return $teamRanks;
    }

    function getAll() {
        $req = "SELECT * FROM " . $this->parent->config['db_prefix'] . "users u";
        $req .= " WHERE instanceID = " . $this->parent->config['current_instance'];
        $req .= " ORDER BY u.name ASC";

        $users = $this->parent->db->select_array($req, $this->max_size);
        if ($this->parent->debug) {
            array_show($users);
        }

        return $users;
    }

    function get($instanceID = false) {
        // Main Query
        $req = "SELECT u.userID, u.name, u.login, u.password, u.email, u.status, u.points, u.nbresults, u.nbscores, u.bonus, u.diff, u.last_rank";
        $req .= ", u.userTeamID, t.name AS team, count(p.userID) AS nbpronos";
        $req .= ", (u.lcp_points + u.lcp_bonus + u.lcp_match) AS lcp_total, u.lcp_points, u.lcp_bonus, u.lcp_match, u.last_rank_lcp";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "users u";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "pronos AS p ON(p.userID = u.userID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "user_teams AS t ON(t.userTeamID = u.userTeamID)";
        $req .= " WHERE u.instanceID = " . ( $instanceID ? $instanceID : $this->parent->config['current_instance'] );
        $req .= " AND (p.scoreA IS NOT null) AND (p.scoreB IS NOT null) AND u.status >= 0";
        $req .= " GROUP BY p.userID";
        $req .= " ORDER BY u.name ASC";

        $users = $this->parent->db->select_array($req, $this->max_size);
        if ($this->parent->debug) {
            array_show($users);
        }

        return $users;
    }

    function getByPhase($phaseID = false) {
        if (!$phaseID) {
            $phaseID = PHASE_ID_ACTIVE - 1;
        }
        if ($phaseID < 0) {
            $phaseID = 0;
        }

        // Main Query
        $req = "SELECT u.userID, u.name, u.login, u.points, u.nbresults, u.nbscores, u.diff, u.last_rank, u.userTeamID, t.name AS team, count(p.userID) AS nbpronos";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "users u";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "pronos AS p ON(p.userID = u.userID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "matchs AS m ON(m.matchID = p.matchID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "user_teams AS t ON(t.userTeamID = u.userTeamID)";
        $req .= " WHERE u.instanceID = " . $this->parent->config['current_instance'];
        $req .= " AND (p.scoreA IS NOT null) AND (p.scoreB IS NOT null) AND u.status >= 0 AND m.phaseID = " . $phaseID;
        $req .= " GROUP BY p.userID";
        $req .= " ORDER BY u.name ASC";

        $users = $this->parent->db->select_array($req, $this->max_size);
        if ($this->parent->debug) {
            array_show($users);
        }

        return $users;
    }

    function getByTeam($userTeamID, $all_users = false) {
        $req = "SELECT DISTINCT(u.userID), u.name, u.login, u.points, u.nbresults, u.nbscores, u.diff, u.last_rank, t.name AS team";
        if (!$all_users) {
            $req .= ", COUNT(p.userID) AS nbpronos";
        }
        $req .= " FROM " . $this->parent->config['db_prefix'] . "users u";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "pronos AS p ON(p.userID = u.userID)";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "user_teams AS t ON(t.userTeamID = u.userTeamID)";
        $req .= " WHERE u.instanceID = " . $this->parent->config['current_instance'];
        if ($userTeamID == 'NULL') {
            $req .= " AND u.userTeamID IS NULL";
        } else {
            $req .= " AND u.userTeamID = " . $userTeamID;
        }
        if (!$all_users) {
            $req .= " AND (p.scoreA IS NOT null) AND (p.scoreB IS NOT null) AND u.status >= 0";
            $req .= " GROUP BY p.userID";
        }
        $req .= " ORDER BY u.name ASC";

        $users = $this->parent->db->select_array($req, $this->max_size);
        if ($this->parent->debug) {
            array_show($users);
        }

        return $users;
    }

    function getByEmail($email) {
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "users u";
        $req .= " WHERE LOWER(email) = '" . strtolower($email) . "'";
        $req .= " AND instanceID = " . $this->parent->config['current_instance'];

        $user = $this->parent->db->select_line($req, $this->max_size);

        return $user;
    }

    function getNumberByTeam($userTeamID) {
        // Main Query
        $req = "SELECT COUNT(u.userID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "users u";
        $req .= " WHERE u.userTeamID = " . $userTeamID . " AND u.status >= 0";
        $req .= " AND instanceID = " . $this->parent->config['current_instance'];

        $nb_users = $this->parent->db->select_one($req);
        if ($this->parent->debug)
            echo $nb_users;

        return $nb_users;
    }

    function getNbActivesUsersByUserTeam($userTeamID) {
        $users = $this->getUsersByUserTeam($userTeamID, true);
        $nbUsersActifs = 0;
        $nbMatchsPlayed = $this->getNbMatchsPlayed();
        foreach ($users as $user) {
            if (($this->parent->bets->getNbPlayedPronosByUser($user['userID']) / $nbMatchsPlayed) > 0.5) {
                $nbUsersActifs++;
            }
        }
        return $nbUsersActifs;
    }

    function getById($id) {
        // Main Query
        $req = "SELECT u.userID, u.name, u.login, u.password, u.email, u.email_preferences, u.points, u.nbresults, u.nbscores, u.diff, u.last_rank, u.status, u.userTeamID, t.name AS team";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "users u";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "user_teams AS t ON(t.userTeamID = u.userTeamID)";
        $req .= " WHERE u.userId = " . $id;
        $req .= " AND u.instanceID = " . $this->parent->config['current_instance'];

        $user = $this->parent->db->select_line($req, $this->max_size);

        return $user;
    }

    function getTeams($orderby = "name", $sens = "ASC") {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "user_teams";
        $req .= " WHERE instanceID = " . $this->parent->config['current_instance'];
        $req .= " ORDER BY $orderby $sens";

        $userTeams = $this->parent->db->select_array($req, $this->max_size);
        if ($this->parent->debug)
            array_show($userTeams);

        return $userTeams;
    }

    function getTeamsByInstance($instanceId) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "user_teams";
        $req .= " WHERE instanceID = " . $instanceId;
        $req .= " ORDER BY name ASC";

        $userTeams = $this->parent->db->select_array($req, $this->max_size);
        if ($this->parent->debug) {
            array_show($userTeams);
        }

        return $userTeams;
    }

    function getTeamByNameAndInstance($name, $instanceId) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "user_teams";
        $req .= " WHERE name = '" . $name . "'";
        $req .= " AND instanceID = " . $instanceId;

        $user_team = $this->parent->db->select_line($req, $this->max_size);

        return $user_team;
    }

    function getNumberOf($instanceID = false) {
        // Main Query
        $req = "SELECT count(DISTINCT u.userID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "users u";
        $req .= " WHERE u.status >= 0";
        $req .= " AND instanceID = " . ($instanceID ? $instanceID : $this->parent->config['current_instance']);

        $nb_users = $this->parent->db->select_one($req);

        return $nb_users;
    }

    function getNumberOfActiveOnes($instanceID = false) {
        // Main Query
        $req = "SELECT count(DISTINCT u.userID)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "users u";
        $req .= " RIGHT JOIN " . $this->parent->config['db_prefix'] . "pronos AS p ON(p.userID = u.userID)";
        $req .= " WHERE u.status >= 0";
        $req .= " AND u.instanceID = " . ($instanceID ? $instanceID : $this->parent->config['current_instance']);

        $nb_users = $this->parent->db->select_one($req);

        return $nb_users;
    }

    function updateTeamRanking() {
        $userTeams = $this->getTeams();
        $userTeamsView = array();
        $teamRanks = $this->getTeamsRank();
        $nbMatchsPlayed = $this->parent->games->getNumberOfPlayedOnes();

        foreach ($userTeams as $userTeam) {
            $userTeam['avgPoints'] = 0;
            $userTeam['maxPoints'] = 0;
            $userTeam['totalPoints'] = 0;

            $users = $this->getByTeam($userTeam['userTeamID']);
            $nbUsersActifs = 0;
            foreach ($users as $user) {
                if (($this->parent->bets->getNumberOfPlayedOnesByUser($user['userID']) / $nbMatchsPlayed) > 0.5) {
                    $userTeam['totalPoints'] += $user['points'];
                    if ($user['points'] > $userTeam['maxPoints']) {
                        $userTeam['maxPoints'] = $user['points'];
                    }
                    $nbUsersActifs++;
                }
            }
            if ($nbUsersActifs > 0) {
                $userTeam['avgPoints'] = round($userTeam['totalPoints'] / $nbUsersActifs, 2);
            }
            $userTeamsView[] = $userTeam;
        }

        // MaJ BDD
        $is_rank_update = $this->parent->settings->isRankToUpdate();
        usort($userTeamsView, "compare_user_teams");
        for ($i = 0; $i < sizeof($userTeamsView); $i++) {
            $userTeam = $userTeamsView[$i];
            //$userTeam['rank'] = ($i + 1);
            $userTeam['rank'] = NULL;
            if ($teamRanks[$userTeam['userTeamID']])
                $userTeam['rank'] = $teamRanks[$userTeam['userTeamID']];

            if ($is_rank_update) {
                $this->parent->db->exec_query("UPDATE " . $this->parent->config['db_prefix'] . "user_teams SET avgPoints = " . $userTeam['avgPoints'] . ", totalPoints = " . $userTeam['totalPoints'] . ", maxPoints = " . $userTeam['maxPoints'] . ", lastRank = " . $userTeam['rank'] . " WHERE userTeamID=" . $userTeam['userTeamID']);
            } else {
                $this->parent->db->exec_query("UPDATE " . $this->parent->config['db_prefix'] . "user_teams SET avgPoints = " . $userTeam['avgPoints'] . ", totalPoints = " . $userTeam['totalPoints'] . ", maxPoints = " . $userTeam['maxPoints'] . " WHERE userTeamID=" . $userTeam['userTeamID']);
            }
        }
        if ($is_rank_update) {
            $this->parent->settings->setLastGenerate();
        }
    }

    function updateRankingLCP() {
        $phasesPlayed = $this->parent->phases->getPlayedOnes();
        $users = array();
        $ranks = $this->getRanksLCP();

        // Phases
        foreach ($phasesPlayed as $phase) {
            $usersPhase = $this->getRankingByPhase($phase['phaseID']);
            $nbUsersPhase = sizeof($usersPhase);

            if ($nbUsersPhase > 0) {
                usort($usersPhase, "compare_users_simple_reverse");

                $lastNbPoints = $usersPhase[0]['points'];
                $cptRank = 3;

                foreach ($usersPhase as $userPhase) {
                    $userID = $userPhase['userID'];
                    if (!isset($users[$userPhase['userID']])) {
                        $users[$userID] = array();
                        $users[$userID]['userID'] = $userID;
                        $users[$userID]['lcp_points'] = 0;
                        $users[$userID]['lcp_bonus'] = 0;
                        $users[$userID]['lcp_match'] = 0;
                        $users[$userID]['rank_lcp'] = 'NULL';
                        if ($ranks[$userID])
                            $users[$userID]['rank_lcp'] = $ranks[$userID];
                    }

                    // Points LCP
                    if ($userPhase['points'] > $lastNbPoints)
                        $cptRank--;
                    if ($cptRank == 3)
                        $users[$userID]['lcp_points'] += 3;
                    elseif ($cptRank == 2)
                        $users[$userID]['lcp_points'] += 2;
                    elseif ($cptRank == 1)
                        $users[$userID]['lcp_points'] += 1;
                    $lastNbPoints = $userPhase['points'];
                }

                // Users
                foreach ($usersPhase as $userPhase) {
                    $userID = $userPhase['userID'];
                    if (isset($userID)) {
                        // Bonus LCP
                        if ($userPhase['points'] == 0)
                            $users[$userID]['lcp_bonus'] += 5;
                        elseif ($userPhase['points'] == 1)
                            $users[$userID]['lcp_bonus'] += 3;
                        elseif ($userPhase['points'] == 2)
                            $users[$userID]['lcp_bonus'] += 2;
                        elseif ($userPhase['points'] == 3)
                            $users[$userID]['lcp_bonus'] += 1;

                        // Match LCP
                        $pronoLCP = $this->parent->bets->getLCPBetsByPhase($userID, $phase['phaseID']);
                        if ($pronoLCP != null) {
                            $resPronoLCP = $this->parent->settings->computeNbPtsProno($phase, $pronoLCP['status'], $pronoLCP['scoreMatchA'], $pronoLCP['scoreMatchB'], $pronoLCP['scorePronoA'], $pronoLCP['scorePronoB']);
                            if ($resPronoLCP['res'] == 0)
                                $users[$userID]['lcp_match'] += 1;
                        }
                    }
                }
            }
        }

        // MaJ BDD
        $is_rank_to_update = $this->parent->settings->isRankToUpdate(true);
        foreach ($users as $ID => $user) {
            if ($is_rank_to_update) {
                $this->parent->db->exec_query("UPDATE " . $this->parent->config['db_prefix'] . "users SET lcp_points = " . $user['lcp_points'] . ", lcp_bonus = " . $user['lcp_bonus'] . ", lcp_match = " . $user['lcp_match'] . ", last_rank_lcp = " . $user['rank_lcp'] . " WHERE userID=" . $ID . "");
            } else {
                $this->parent->db->exec_query("UPDATE " . $this->parent->config['db_prefix'] . "users SET lcp_points = " . $user['lcp_points'] . ", lcp_bonus = " . $user['lcp_bonus'] . ", lcp_match = " . $user['lcp_match'] . " WHERE userID=" . $ID . "");
            }
        }
        if ($is_rank_to_update) {
            $this->parent->settings->setLastGenerate(true);
        }
        return;
    }

    function updateRanking() {
        $matchs = $this->parent->games->get();
        $users = array();
        $ranks = $this->getRanks();

        // Points pr les matchs
        foreach ($matchs as $match) {
            $pronos = $this->parent->bets->getByGame($match['matchID']);
            $phase = $this->parent->phases->getById($match['phaseID']);

            foreach ($pronos as $prono) {
                if (!isset($users[$prono['userID']])) {
                    $users[$prono['userID']] = array();
                    $users[$prono['userID']]['userID'] = $prono['userID'];
                    $users[$prono['userID']]['points'] = 0;
                    $users[$prono['userID']]['nbscores'] = 0;
                    $users[$prono['userID']]['bonus'] = 0;
                    $users[$prono['userID']]['diff'] = 0;
                    $users[$prono['userID']]['nbresults'] = 0;
                    $users[$prono['userID']]['rank'] = 'NULL';
                    if ($ranks[$prono['userID']])
                        $users[$prono['userID']]['rank'] = $ranks[$prono['userID']];
                }

                if (($prono['scorePronoA'] != NULL) && ($prono['scorePronoB'] != NULL) && ($match['scoreMatchA'] != NULL) && ($match['scoreMatchB'] != NULL)) {
                    $resProno = $this->parent->settings->computeNbPtsProno($phase, $prono['status'], $match['scoreMatchA'], $match['scoreMatchB'], $prono['scorePronoA'], $prono['scorePronoB']);
                    $users[$prono['userID']]['nbresults'] += $resProno['res'];
                    $users[$prono['userID']]['points'] += $resProno['points'];
                    $users[$prono['userID']]['nbscores'] += $resProno['score'];
                    $users[$prono['userID']]['bonus'] += $resProno['bonus'];
                    $users[$prono['userID']]['diff'] += $resProno['diff'];
                }
            }
        }

        // MaJ BDD
        $is_rank_to_update = $this->parent->settings->isRankToUpdate();
        foreach ($users as $ID => $user) {
            if ($is_rank_to_update) {
                $this->parent->db->exec_query("UPDATE " . $this->parent->config['db_prefix'] . "users SET points = " . $user['points'] . ", nbresults = " . $user['nbresults'] . ", nbscores = " . $user['nbscores'] . ", bonus = " . $user['bonus'] . ", diff = " . $user['diff'] . ", last_rank = " . $user['rank'] . " WHERE userID=" . $ID . "");
            } else {
                $this->parent->db->exec_query("UPDATE " . $this->parent->config['db_prefix'] . "users SET points = " . $user['points'] . ", nbresults = " . $user['nbresults'] . ", nbscores = " . $user['nbscores'] . ", bonus = " . $user['bonus'] . ", diff = " . $user['diff'] . " WHERE userID=" . $ID . "");
            }
        }
        if ($is_rank_to_update) {
            $this->parent->settings->setLastGenerate();
        }
        
        // MaJ libelle etat classement
        $this->parent->settings->setLastGenerateLabel();
        
        return;
    }

    function getRankingByPhase($phaseID = false) {
        if (!$phaseID)
            $phaseID = PHASE_ID_ACTIVE - 1;
        if ($phaseID < 0)
            $phaseID = 0;

        $games = $this->parent->games->getByPhase($phaseID);
        $phase = $this->parent->phases->getById($phaseID);
        $users = array();

        // points for games
        foreach ($games as $game) {
            $bets = $this->parent->bets->getByGame($game['matchID']);

            foreach ($bets as $bet) {
                if (!isset($users[$bet['userID']])) {
                    $user = $this->getById($bet['userID']);
                    $users[$bet['userID']] = array();
                    $users[$bet['userID']]['userID'] = $bet['userID'];
                    $users[$bet['userID']]['points'] = 0;
                    $users[$bet['userID']]['nbscores'] = 0;
                    $users[$bet['userID']]['nbresults'] = 0;
                    $users[$bet['userID']]['bonus'] = 0;
                    $users[$bet['userID']]['diff'] = 0;
                    $users[$bet['userID']]['lcp_points'] = 0;
                    $users[$bet['userID']]['lcp_bonus'] = 0;
                    $users[$bet['userID']]['lcp_match'] = 0;
                    $users[$bet['userID']]['rank'] = 'NULL';
                    $users[$bet['userID']]['login'] = $user['login'];
                    $users[$bet['userID']]['name'] = $user['name'];
                    $users[$bet['userID']]['team'] = $user['team'];
                }

                if (($bet['scorePronoA'] != NULL) && ($bet['scorePronoB'] != NULL) && ($game['scoreMatchA'] != NULL) && ($game['scoreMatchB'] != NULL)) {
                    $resProno = $this->parent->settings->computeNbPtsProno($phase, $bet['status'], $game['scoreMatchA'], $game['scoreMatchB'], $bet['scorePronoA'], $bet['scorePronoB']);
                    $users[$bet['userID']]['points'] += $resProno['points'];
                    $users[$bet['userID']]['nbresults'] += $resProno['res'];
                    $users[$bet['userID']]['nbscores'] += $resProno['score'];
                    $users[$bet['userID']]['bonus'] += $resProno['bonus'];
                    $users[$bet['userID']]['diff'] += $resProno['diff'];
                }
            }
        }

        return $users;
    }

    function getRankingLCPByPhase($phaseID = false) {
        if (!$phaseID)
            $phaseID = PHASE_ID_ACTIVE - 1;
        if ($phaseID < 0)
            $phaseID = 0;

        $phase = $this->parent->phases->getById($phaseID);

        $usersPhase = $this->getRankingByPhase($phase['phaseID']);
        $nbUsersPhase = sizeof($usersPhase);
        $users = array();

        if ($nbUsersPhase > 0) {
            usort($usersPhase, "compare_users_simple_reverse");

            $lastNbPoints = $usersPhase[0]['points'];
            $cptRank = 3;

            foreach ($usersPhase as $userPhase) {
                $userID = $userPhase['userID'];
                if (!isset($users[$userPhase['userID']])) {
                    $users[$userID] = array();
                    $users[$userID]['userID'] = $userID;
                    $users[$userID]['name'] = $userPhase['name'];
                    $users[$userID]['login'] = $userPhase['login'];
                    $users[$userID]['team'] = $userPhase['team'];
                    $users[$userID]['lcp_total'] = 0;
                    $users[$userID]['lcp_points'] = 0;
                    $users[$userID]['lcp_bonus'] = 0;
                    $users[$userID]['lcp_match'] = 0;
                }

                // Points LCP
                if ($userPhase['points'] > $lastNbPoints)
                    $cptRank--;
                if ($cptRank == 3)
                    $users[$userID]['lcp_points'] += 3;
                elseif ($cptRank == 2)
                    $users[$userID]['lcp_points'] += 2;
                elseif ($cptRank == 1)
                    $users[$userID]['lcp_points'] += 1;
                $lastNbPoints = $userPhase['points'];
            }

            // Users
            foreach ($usersPhase as $userPhase) {
                $userID = $userPhase['userID'];
                if (isset($userID)) {
                    // Bonus LCP
                    if ($userPhase['points'] == 0)
                        $users[$userID]['lcp_bonus'] += 5;
                    elseif ($userPhase['points'] == 1)
                        $users[$userID]['lcp_bonus'] += 3;
                    elseif ($userPhase['points'] == 2)
                        $users[$userID]['lcp_bonus'] += 2;
                    elseif ($userPhase['points'] == 3)
                        $users[$userID]['lcp_bonus'] += 1;

                    // Match LCP
                    $betLCP = $this->parent->bets->getLCPBetsByPhase($userID, $phase['phaseID']);
                    if ($betLCP != null) {
                        $resBetLCP = $this->parent->settings->computeNbPtsProno($phase, $betLCP['status'], $betLCP['scoreMatchA'], $betLCP['scoreMatchB'], $betLCP['scorePronoA'], $betLCP['scorePronoB']);
                        if ($resBetLCP['res'] == 0)
                            $users[$userID]['lcp_match'] += 1;
                    }

                    // Total LCP
                    $users[$userID]['lcp_total'] = $users[$userID]['lcp_points'] + $users[$userID]['lcp_bonus'] + $users[$userID]['lcp_match'];
                }
            }
        }
        return $users;
    }

    function setNewPassword($userID) {
        $user = $this->getById($userID);
        if (!$user) {
            return false;
        }
        $new_pass = newPassword(8);

        $req = 'UPDATE ' . $this->parent->config['db_prefix'] . 'users';
        $req .= ' SET password = \'' . md5($new_pass) . '\'';
        $req .= ' WHERE "userID" = ' . $userID . '';

        if ($this->parent->db->exec_query($req)) {
            return $new_pass;
        } else {
            return false;
        }
    }

    function getActiveUsersWhoHaveNotBet($nbDays) {
        $req = "SELECT DISTINCT u.userID, u.login, u.name, u.email FROM l1__users AS u";
        $req .= " RIGHT JOIN l1__pronos AS b ON(u.userID = b.userID)";
        $req .= " WHERE instanceID = " . $this->parent->config['current_instance'];
        $req .= " AND u.email_preferences LIKE '1%' AND u.userID NOT IN (";
        $req .= " SELECT DISTINCT u.userID FROM l1__users AS u";
        $req .= " LEFT JOIN l1__pronos AS b ON(u.userID = b.userID)";
        $req .= " WHERE b.matchID IN (";
        $req .= " SELECT m.matchID FROM l1__matchs AS m";
        $req .= " WHERE DATEDIFF(m.date, NOW()) >= 0 AND DATEDIFF(m.date, NOW()) <= " . $nbDays . "))";

        $users = $this->parent->db->select_array($req, $this->max_size);
        if ($this->parent->debug) {
            array_show($users);
        }
        return $users;
    }

}

?>