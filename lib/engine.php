<?

include_once(BASE_PATH . 'lib/define.inc.php');
include_once(BASE_PATH . 'lib/config.inc.php');
include_once(BASE_PATH . 'lang/' . $config['lang'] . '.inc.php');
include_once(BASE_PATH . 'lib/functions.inc.php');
include_once(BASE_PATH . 'lib/db.php');
include_once(BASE_PATH . 'lib/template.php');
include_once(BASE_PATH . 'lib/bets.php');
include_once(BASE_PATH . 'lib/games.php');
include_once(BASE_PATH . 'lib/instances.php');
include_once(BASE_PATH . 'lib/phases.php');
include_once(BASE_PATH . 'lib/settings.php');
include_once(BASE_PATH . 'lib/stats.php');
include_once(BASE_PATH . 'lib/tags.php');
include_once(BASE_PATH . 'lib/teams.php');
include_once(BASE_PATH . 'lib/users.php');

class Engine {

    var $db;
    var $debug;
    var $config;
    var $lang;
    var $template;
    var $start_time;
    var $blocks_loaded;
    var $bets;
    var $games;
    var $instances;
    var $phases;
    var $stats;
    var $tags;
    var $teams;
    var $users;
    var $settings;

    /*     * *************** */
    /*  CONTRUCTOR	  */
    /*     * *************** */

    function Engine($admin = false, $debug = false) {
        global $config;
        global $lang;

        $time = time();
        $this->start_time = get_moment();
        $this->db = new DB();
        $this->db->set_debug($debug);
        $this->debug = $debug;
        $this->config = $config;
        $this->lang = $lang;
        $this->step = 0;
        $this->template_location = BASE_PATH . "template/" . $config['template'];
        $this->template_web_location = WEB_PATH . "template/" . $config['template'];
        $this->template = new Template($this->template_location);
        $this->blocks_loaded = array();

        $this->bets = new Bets($this);
        $this->games = new Games($this);
        $this->instances = new Instances($this);
        $this->phases = new Phases($this);
        $this->settings = new Settings($this);
        $this->stats = new Stats($this);
        $this->tags = new Tags($this);
        $this->teams = new Teams($this);
        $this->users = new Users($this);
    }

    function display() {
        foreach ($this->blocks_loaded as $block)
            $this->template->pparse($block);
    }

    function login($login, $pass) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->config['db_prefix'] . "users ";
        $req .= " WHERE LOWER(login) = '" . addslashes(strtolower($login)) . "'";
        $req .= " AND password = '" . md5($pass) . "'";
        $req .= " AND instanceID = " . $this->config['current_instance'];
        $req .= " AND status >= 0";

        $user = $this->db->select_line($req, $nb_user);

        if ($nb_user == 1) {
            $_SESSION['username'] = $user['name'];
            $_SESSION['nom_joueur'] = $user['name'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['status'] = $user['status'];
            if ($user['status'] == 1) {
                $this->admin = true;
            }
            return true;
        } else {
            return false;
        }
    }

    function isLogged() {
        return (isset($_SESSION['userID']));
    }

    function isAdmin() {
        return (isset($_SESSION['status']) && $_SESSION['status'] == 1);
    }

    function loadTags($userTeamID = -1, $start = false) {
        $this->template->set_filenames(array('tags' => 'tags.tpl'));

        $start = $start ? $start : 0;
        $tags = $this->tags->get($start, 10, $userTeamID);

        $nb_tags = $this->tags->getNumberOf($userTeamID);
        $nb_pages = ceil($nb_tags / 10);
        $max = ceil($nb_tags / 10);
        $page = ceil(($start + 1) / 10);
        $prev = ($page - 2) * 10;
        $next = $page * 10;

        if ($max <= 1) {
            $navig = "";
        } elseif ($page == 1) {
            $navig = "<strong><a href=\"#\" onclick=\"getTags(" . $userTeamID . ", " . $next . ");\">>></a></strong>";
        } elseif (($page > 1) && ($page < $max)) {
            $navig = "<strong><a href=\"#\" onclick=\"getTags(" . $userTeamID . ", " . $prev . ");\"><<</a> <a href=\"#\" onclick=\"getTags(" . $userTeamID . ", " . $next . ");\">>></a></strong>";
        } elseif ($page == $max) {
            $navig = "<strong><a href=\"#\" onclick=\"getTags(" . $userTeamID . ", " . $prev . ");\"><<</a></strong>";
        }

        foreach ($tags as $tag) {
            if ($tag['userID'] == $_SESSION['userID'] || $this->isAdmin())
                $del_img = "<a href=\"#\"><img src=\"" . $this->template_web_location . "/images/del.png\" onclick=\"delTag(" . $tag['tagID'] . ", " . $userTeamID . ")\" border=\"0\"/></a>";
            else
                $del_img = "";

            $login_str = stripslashes($tag['login']);
            //$login_str = utf8_encode($login_str);

            $tag_str = stripslashes($tag['tag']);
            //$tag_str = utf8_encode($tag_str);

            $this->template->assign_block_vars('tags', array('ID' => $tag['tagID'],
                'DEL_IMG' => $del_img,
                'DATE' => $tag['date_str'],
                'USER' => $login_str,
                'TEXT' => $tag_str));
        }
        $this->template->assign_vars(array('NAVIG' => $navig,
            'TAG_SEPARATOR' => $this->config['tag_separator']));

        $this->blocks_loaded[] = 'tags';
        $this->display();
    }

    function loadRanking($userID, $instanceID = false) {
        $this->template->set_filenames(array('ranking' => 'ranking.tpl'));

        $instance = NULL;
        if($instanceID) {
            $instance = $this->instances->getById($instanceID);
            $this->template->set_filenames(array('ranking' => 'ranking_simple.tpl'));
        }
        else {
            $this->template->set_filenames(array('ranking' => 'ranking.tpl'));
        }
        $users = $this->users->get($instanceID);
        $nbTotalUsers = $this->users->getNumberOf($instanceID);
        
        $infos = array(
            'LAST_GENERATE_LABEL' => $this->settings->getLastGenerateLabel(),
            'INSTANCE_ID' => $instanceID,
            'INSTANCE_NAME' => ( $instance ? $instance['name'] : "" ),
            'GENERAL_CUP_LABEL' => $this->config['general_cup_label'],
            'LCP_LABEL' => $this->config['lcp_label'],
            'LCP_SHORT_LABEL' => $this->config['lcp_short_label'],
            'PERFECT_CUP_LABEL' => $this->config['perfect_cup_label'],
            'TPL_WEB_PATH' => $this->template_web_location
        );
        $this->template->assign_vars($infos);

        if (($nbTotalUsers > 0) && (sizeof($users) > 0)) {
            usort($users, "compare_users");
            $nbMatchs = $this->games->getNbMatchsByPhase($this->phases->getNextPhaseIdToBet($instanceID));

            $i = 1;
            $j = 0;
            $k = 0;
            $last_user = $users[0];

            foreach ($users as $user) {
                if ($user['nbpronos'] == 0) {
                    $nbTotalUsers--;
                    continue;
                }
                if (compare_users($user, $last_user) != 0) {
                    $i = $j + 1;
                }
                $nbPronosPlayed = $this->bets->getNumberOfPlayedOnesByUserAndPhase($user['userID'], $this->phases->getNextPhaseIdToBet());

                $evol = $user['last_rank'] - $i;

                if ($evol == 0)
                    $img = "egal.png";
                elseif ($evol > 5)
                    $img = "arrow_up2.png";
                elseif ($evol > 0)
                    $img = "arrow_up1.png";
                elseif ($evol < -5)
                    $img = "arrow_down2.png";
                elseif ($evol < 0)
                    $img = "arrow_down1.png";
                if ($evol > 0)
                    $evol = "+" . $evol;

                $bg_color = "";
                if ($userID == $user['userID']) {
                    $bg_color = " style=\"background-color:#FBD670;\"";
                } elseif ($i <= 3) {
                    $bg_color = " style=\"background-color:#FEECA5;\"";
                } elseif ($i > ($nbTotalUsers - 3)) {
                    $bg_color = " style=\"background-color:#FEECA5;\"";
                }

                $usersView[$k++] = array(
                    'RANK' => $i,
                    'LAST_RANK' => "<img src=\"" . $this->template_web_location . "/images/" . $img . "\" alt=\"\" /><br/><span style=\"text-align:center;font-size:70%;\">(" . $evol . ")</span>",
                    'NB_BETS' => (($nbPronosPlayed != $nbMatchs) && !$instanceID) ? "(<span style=\"color:red;\">" . ($nbMatchs - $nbPronosPlayed) . " pronos à jouer</span>)" : "",
                    'ID' => $user['userID'],
                    'NAME' => $user['name'],
                    'LOGIN' => $user['login'],
                    'VIEW_BETS' => "<a href=\"/?op=edit_pronos&user=" . $user['userID'] . "\">",
                    'POINTS' => $user['points'],
                    'NBRESULTS' => $user['nbresults'],
                    'NBSCORES' => $user['nbscores'],
                    'BONUS' => $user['bonus'],
                    'LCP' => $user['lcp_points'] + $user['lcp_bonus'] + $user['lcp_match'],
                    'DIFF' => $user['diff'],
                    'TEAM' => $user['team'],
                    'BG_COLOR' => $bg_color
                );
                $last_user = $user;
                $j++;

                $this->template->assign_block_vars('users', $usersView[$k - 1]);
            }
        }

        $this->blocks_loaded[] = 'ranking';
    }

    function loadUserTeamRanking() {
        $userTeams = $this->getUserTeams("lastRank");
        $userTeamsView = array();

        $diffRank = 0;
        foreach ($userTeams as $userTeam) {
            //$users = $this->users->getUsersByUserTeam($userTeam['userTeamID']);
            $userTeam['nbUsersActifs'] = $this->users->getNbActivesUsersByUserTeam($userTeam['userTeamID']); //sizeof($users);
            if ($userTeam['nbUsersActifs'] < 3) {
                $diffRank--;
                continue;
            }
            $userTeam['nbUsersTotal'] = $this->users->getNbUsersByUserTeam($userTeam['userTeamID']);
            $userTeam['lastRank'] += $diffRank;
            $userTeamsView[] = $userTeam;
        }

        $rank = 1;
        $last_team = $userTeams[0];
        usort($userTeamsView, "compare_user_teams");
        for ($i = 0; $i < sizeof($userTeamsView); $i++) {
            if (compare_user_teams($userTeamsView[$i], $last_team) != 0)
                $rank = $i + 1;
            $userTeamsView[$i]['rank'] = $rank;

            $evol = $userTeamsView[$i]['lastRank'] - $rank;
            if ($evol == 0)
                $img = "egal.png";
            elseif ($evol > 5)
                $img = "arrow_up2.png";
            elseif ($evol > 0)
                $img = "arrow_up1.png";
            elseif ($evol < -5)
                $img = "arrow_down2.png";
            elseif ($evol < 0)
                $img = "arrow_down1.png";
            if ($evol > 0)
                $evol = "+" . $evol;
            $userTeamsView[$i]['lastRank'] = "<img src=\"" . $this->template_web_location . "/images/" . $img . "\" alt=\"\" /><br/><span style=\"text-align:center;font-size:70%;\">(" . $evol . ")</span>";

            $last_team = $userTeamsView[$i];
        }
        return $userTeamsView;
    }

    function loadRankingInTeams($userTeamId) {
        $users = $this->users->getByTeam($userTeamId);

        if (sizeof($users) > 0) {
            usort($users, "compare_users");
            $nbMatchs = $this->games->getNumberOf();

            $i = 1;
            $j = 0;
            $k = 0;
            $last_user = $users[0];

            foreach ($users as $user) {
                if ($user['nbpronos'] == 0)
                    continue;
                if (compare_users($user, $last_user) != 0)
                    $i = $j + 1;

                $usersView[$k++] = array(
                    'RANK' => $i,
                    'NB_BETS' => ($user['nbpronos'] != $nbMatchs) ? "(<span style=\"color:red;\">" . $user['nbpronos'] . "/" . $nbMatchs . "</span>)" : "",
                    'ID' => $user['userID'],
                    'NAME' => $user['name'],
                    'LOGIN' => $user['login'],
                    'VIEW_BETS' => "<a href=\"/?op=edit_pronos&user=" . $user['userID'] . "\">",
                    'POINTS' => $user['points'],
                    'NBRESULTS' => $user['nbresults'],
                    'NBSCORES' => $user['nbscores'],
                    'DIFF' => $user['diff'],
                    'TEAM' => $user['team']
                );
                $last_user = $user;
                $j++;
            }

            return $usersView;
        }
        else
            return array();
    }

    function loadRankingLCP($userID) {
        $this->template->set_filenames(array('ranking_lcp' => 'ranking_lcp.tpl'));

        $infos = array(
            'GENERAL_CUP_LABEL' => $this->config['general_cup_label'],
            'LCP_LABEL' => $this->config['lcp_label'],
            'PERFECT_CUP_LABEL' => $this->config['perfect_cup_label']
        );
        $this->template->assign_vars($infos);

        $users = $this->users->get();
        $nbTotalUsers = sizeof($users);
        if ($nbTotalUsers > 0) {
            usort($users, "compare_users_lcp");

            $i = 1;
            $j = 0;
            $k = 0;
            $last_user = $users[0];

            foreach ($users as $user) {
                if ($user['nbpronos'] == 0) {
                    $nbTotalUsers--;
                    continue;
                }
                if (compare_users_lcp($user, $last_user) != 0)
                    $i = $j + 1;

                $evol = $user['last_rank_lcp'] - $i;

                if ($evol == 0)
                    $img = "egal.png";
                elseif ($evol > 5)
                    $img = "arrow_up2.png";
                elseif ($evol > 0)
                    $img = "arrow_up1.png";
                elseif ($evol < -5)
                    $img = "arrow_down2.png";
                elseif ($evol < 0)
                    $img = "arrow_down1.png";
                if ($evol > 0)
                    $evol = "+" . $evol;

                $bg_color = "";
                if ($userID == $user['userID']) {
                    $bg_color = " style=\"background-color:#FBD670;\"";
                } elseif ($i <= 3) {
                    $bg_color = " style=\"background-color:#FEECA5;\"";
                } elseif ($i > ($nbTotalUsers - 3)) {
                    $bg_color = " style=\"background-color:#FEECA5;\"";
                }

                $usersView[$k++] = array(
                    'RANK' => $i,
                    'LAST_RANK' => "<img src=\"" . $this->template_web_location . "/images/" . $img . "\" alt=\"\" /><br/><span style=\"text-align:center;font-size:70%;\">(" . $evol . ")</span>",
                    'ID' => $user['userID'],
                    'NAME' => $user['name'],
                    'LOGIN' => $user['login'],
                    'VIEW_BETS' => "<a href=\"/?op=edit_pronos&user=" . $user['userID'] . "\">",
                    'LCP_TOTAL' => ($user['lcp_points'] + $user['lcp_bonus'] + $user['lcp_match']),
                    'LCP_POINTS' => $user['lcp_points'],
                    'LCP_BONUS' => $user['lcp_bonus'],
                    'LCP_MATCH' => $user['lcp_match'],
                    'TEAM' => $user['team'],
                    'BG_COLOR' => $bg_color
                );
                $last_user = $user;
                $j++;

                $this->template->assign_block_vars('users', $usersView[$k - 1]);
            }
        }

        $this->blocks_loaded[] = 'ranking_lcp';
    }

    function loadRankingPerfect($userID) {
        $this->template->set_filenames(array('ranking_perfect' => 'ranking_perfect.tpl'));

        $infos = array(
            'GENERAL_CUP_LABEL' => $this->config['general_cup_label'],
            'LCP_LABEL' => $this->config['lcp_label'],
            'PERFECT_CUP_LABEL' => $this->config['perfect_cup_label']
        );
        $this->template->assign_vars($infos);

        $users = $this->users->get();
        $nbTotalUsers = sizeof($users);
        if ($nbTotalUsers > 0) {
            usort($users, "compare_users_perfect");

            $i = 1;
            $j = 0;
            $k = 0;
            $last_user = $users[0];

            foreach ($users as $user) {
                $user['nbpronos'] = $this->bets->getNumberOfPlayedOnesByUser($user['userID']);
                if ($user['nbpronos'] == 0) {
                    $nbTotalUsers--;
                    continue;
                }
                if (compare_users_perfect($user, $last_user) != 0)
                    $i = $j + 1;


                $bg_color = "";
                if ($userID == $user['userID']) {
                    $bg_color = " style=\"background-color:#FBD670;\"";
                } elseif ($i <= 3) {
                    $bg_color = " style=\"background-color:#FEECA5;\"";
                } elseif ($i > ($nbTotalUsers - 3)) {
                    $bg_color = " style=\"background-color:#FEECA5;\"";
                }

                $usersView[$k++] = array(
                    'RANK' => $i,
                    'ID' => $user['userID'],
                    'NAME' => $user['name'],
                    'LOGIN' => $user['login'],
                    'VIEW_BETS' => "<a href=\"/?op=edit_pronos&user=" . $user['userID'] . "\">",
                    'NBSCORES' => $user['nbscores'],
                    'NBPRONOS' => $user['nbpronos'],
                    'SCORERATE' => round(($user['nbscores'] / $user['nbpronos']) * 100, 1) . " %",
                    'TEAM' => $user['team'],
                    'BG_COLOR' => $bg_color
                );
                $last_user = $user;
                $j++;

                $this->template->assign_block_vars('users', $usersView[$k - 1]);
            }
        }

        $this->blocks_loaded[] = 'ranking_perfect';
    }

    function loadMyProfile($userID, $message = "") {
        $this->template->set_filenames(array('my_profile' => 'my_profile.tpl'));

        $user = $this->users->getById($userID);
        $infos = array(
            'ID' => $user['userID'],
            'NAME' => $user['name'],
            'LOGIN' => $user['login'],
            'EMAIL' => $user['email'],
            'TEAM' => $user['team'],
            'MAIL_1' => ($user['email_preferences'][0] == "1") ? "checked=\"checked\" " : "",
            'MAIL_2' => ($user['email_preferences'][1] == "1") ? "checked=\"checked\" " : "",
            'IMG_PATH' => $this->template_web_location . "/images",
            'MESSAGE' => $message
        );
        $this->template->assign_vars($infos);

        $this->blocks_loaded[] = 'my_profile';
    }

    function loadLogin($message = "") {
        $this->template->set_filenames(array('login' => 'login.tpl'));

        $infos = array(
            'IMG_PATH' => $this->template_web_location . "/images",
            'MESSAGE' => $message
        );
        $this->template->assign_vars($infos);

        $this->blocks_loaded[] = 'login';
    }

    function loadForgotIDs() {
        $this->template->set_filenames(array('forgot_ids' => 'forgot_ids.tpl'));

        $this->template->assign_vars(array(
            'TPL_WEB_PATH' => $this->template_web_location,
            'LABEL_EMAIL' => $this->lang['LABEL_EMAIL']
        ));

        $this->blocks_loaded[] = 'forgot_ids';
    }

    function loadUserStats($userID) {
        $this->template->set_filenames(array('user_stats' => 'user_stats.tpl'));

        $user = $this->users->getById($userID);
        $types = array(1 => "Classement", 2 => "Nb de points par journée", 3 => "Total de pts / Nb de résultats / Nb de perfects");

        foreach ($types as $id => $type) {
            $this->template->assign_block_vars('stats', array(
                'TYPE' => $type,
                'ID' => $id
            ));
        }

        $this->template->assign_vars(array(
            'USER_LOGIN' => $user['login'],
            'USER_ID' => $user['userID']
        ));

        $this->blocks_loaded[] = 'user_stats';
    }

    function loadRankingByPhase($phaseID = false) {
        $this->template->set_filenames(array('ranking_phase' => 'ranking_phase.tpl'));

        $infos = array(
            'NB_GAMES_PLAYED' => $this->games->getNbMatchsPlayedByPhase($phaseID),
            'NB_GAMES_TOTAL' => $this->games->getNbMatchsByPhase($phaseID),
            'GENERAL_CUP_LABEL' => $this->config['general_cup_label'],
            'LCP_LABEL' => $this->config['lcp_label'],
            'PERFECT_CUP_LABEL' => $this->config['perfect_cup_label']
        );
        $this->template->assign_vars($infos);

        $phases = $this->phases->get('DESC');
        $phaseName = '';
        $users = $this->users->getRankingByPhase($phaseID);

        $selected = '';
        foreach ($phases as $phase) {
            if ($phaseID && ($phase['phaseID'] == $phaseID)) {
                $selected = 'selected="selected"';
                $phaseName = $phase['name'];
            } else {
                $selected = '';
            }

            $this->template->assign_block_vars('phases', array(
                'PHASE_ID' => $phase['phaseID'],
                'SELECTED' => $selected,
                'NAME' => $phase['name']
            ));
        }

        // get previous and next phases
        $phaseConsultee = $this->phases->getById($phaseID);
        $phaseSuivante = $this->phases->getByDirectRoot($phaseID);
        $phasePrecedente = NULL;
        if ($phaseConsultee['phasePrecedente'] != NULL) {
            $phasePrecedente = $this->phases->getById($phaseConsultee['phasePrecedente']);
        }

        if (sizeof($users) > 0) {
            usort($users, "compare_users");

            $i = 1;
            $j = 0;
            $k = 0;
            $last_user = $users[0];

            foreach ($users as $user) {
                if (compare_users($user, $last_user) != 0)
                    $i = $j + 1;

                $usersView[$k++] = array(
                    'RANK' => $i,
                    'ID' => $user['userID'],
                    'NAME' => $user['name'],
                    'LOGIN' => $user['login'],
                    'VIEW_BETS' => "<a href=\"/?op=edit_pronos&user=" . $user['userID'] . "\">",
                    'POINTS' => $user['points'],
                    'NBRESULTS' => $user['nbresults'],
                    'NBSCORES' => $user['nbscores'],
                    'BONUS' => $user['bonus'],
                    'LCP_TOTAL' => ($user['lcp_points'] + $user['lcp_bonus'] + $user['lcp_match']),
                    'DIFF' => $user['diff'],
                    'TEAM' => $user['team']
                );
                $last_user = $user;
                $j++;

                $this->template->assign_block_vars('users', $usersView[$k - 1]);
            }
        }

        $this->template->assign_vars(array(
            'PHASE_NAME' => $phaseName,
            'PREVIOUS_PHASE_VISIBILITY' => ($phasePrecedente == NULL) ? "hidden" : "visible",
            'PREVIOUS_PHASE_ID' => ($phasePrecedente == NULL) ? $phaseID : $phasePrecedente['phaseID'],
            'NEXT_PHASE_VISIBILITY' => ($phaseSuivante == NULL) ? "hidden" : "visible",
            'NEXT_PHASE_ID' => ($phaseSuivante == NULL) ? $phaseID : $phaseSuivante['phaseID']
        ));

        $this->blocks_loaded[] = 'ranking_phase';
    }

    function loadRankingLCPByPhase($phaseID = false) {
        $this->template->set_filenames(array('ranking_phase_lcp' => 'ranking_phase_lcp.tpl'));

        $infos = array(
            'GENERAL_CUP_LABEL' => $this->config['general_cup_label'],
            'LCP_LABEL' => $this->config['lcp_label'],
            'PERFECT_CUP_LABEL' => $this->config['perfect_cup_label']
        );
        $this->template->assign_vars($infos);

        $phases = $this->phases->get('DESC');
        $phaseName = '';
        $users = $this->users->getRankingLCPByPhase($phaseID);

        $selected = '';
        foreach ($phases as $phase) {
            if ($phaseID && ($phase['phaseID'] == $phaseID)) {
                $selected = 'selected="selected"';
                $phaseName = $phase['name'];
            } else {
                $selected = '';
            }

            $this->template->assign_block_vars('phases', array(
                'PHASE_ID' => $phase['phaseID'],
                'SELECTED' => $selected,
                'NAME' => $phase['name']
            ));
        }

        // get previous and next phases
        $phaseConsultee = $this->phases->getById($phaseID);
        $phaseSuivante = $this->phases->getByDirectRoot($phaseID);
        $phasePrecedente = NULL;
        if ($phaseConsultee['phasePrecedente'] != NULL) {
            $phasePrecedente = $this->phases->getById($phaseConsultee['phasePrecedente']);
        }

        if (sizeof($users) > 0) {
            usort($users, "compare_users_lcp");

            $i = 1;
            $j = 0;
            $k = 0;
            $last_user = $users[0];

            foreach ($users as $user) {
                if (compare_users_lcp($user, $last_user) != 0)
                    $i = $j + 1;

                $usersView[$k++] = array(
                    'RANK' => $i,
                    'ID' => $user['userID'],
                    'NAME' => $user['name'],
                    'LOGIN' => $user['login'],
                    'VIEW_BETS' => "<a href=\"/?op=edit_pronos&user=" . $user['userID'] . "\">",
                    'LCP_TOTAL' => ($user['lcp_points'] + $user['lcp_bonus'] + $user['lcp_match']),
                    'LCP_POINTS' => $user['lcp_points'],
                    'LCP_BONUS' => $user['lcp_bonus'],
                    'LCP_MATCH' => $user['lcp_match'],
                    'TEAM' => $user['team']
                );
                $last_user = $user;
                $j++;

                $this->template->assign_block_vars('users', $usersView[$k - 1]);
            }
        }

        $this->template->assign_vars(array(
            'PHASE_NAME' => $phaseName,
            'PREVIOUS_PHASE_VISIBILITY' => ($phasePrecedente == NULL) ? "hidden" : "visible",
            'PREVIOUS_PHASE_ID' => ($phasePrecedente == NULL) ? $phaseID : $phasePrecedente['phaseID'],
            'NEXT_PHASE_VISIBILITY' => ($phaseSuivante == NULL) ? "hidden" : "visible",
            'NEXT_PHASE_ID' => ($phaseSuivante == NULL) ? $phaseID : $phaseSuivante['phaseID']
        ));

        $this->blocks_loaded[] = 'ranking_phase_lcp';
    }

    function loadRankingPerfectByPhase($phaseID = false) {
        $this->template->set_filenames(array('ranking_phase_perfect' => 'ranking_phase_perfect.tpl'));

        $infos = array(
            'GENERAL_CUP_LABEL' => $this->config['general_cup_label'],
            'LCP_LABEL' => $this->config['lcp_label'],
            'PERFECT_CUP_LABEL' => $this->config['perfect_cup_label']
        );
        $this->template->assign_vars($infos);

        $phases = $this->phases->get('DESC');
        $phaseName = '';
        $users = $this->users->getRankingByPhase($phaseID);

        $selected = '';
        foreach ($phases as $phase) {
            if ($phaseID && ($phase['phaseID'] == $phaseID)) {
                $selected = 'selected="selected"';
                $phaseName = $phase['name'];
            } else {
                $selected = '';
            }

            $this->template->assign_block_vars('phases', array(
                'PHASE_ID' => $phase['phaseID'],
                'SELECTED' => $selected,
                'NAME' => $phase['name']
            ));
        }

        // get previous and next phases
        $phaseConsultee = $this->phases->getById($phaseID);
        $phaseSuivante = $this->phases->getByDirectRoot($phaseID);
        $phasePrecedente = NULL;
        if ($phaseConsultee['phasePrecedente'] != NULL) {
            $phasePrecedente = $this->phases->getById($phaseConsultee['phasePrecedente']);
        }

        if (sizeof($users) > 0) {
            usort($users, "compare_users_perfect");

            $i = 1;
            $j = 0;
            $k = 0;
            $last_user = $users[0];

            foreach ($users as $user) {
                if (compare_users_perfect($user, $last_user) != 0)
                    $i = $j + 1;

                $user['nbpronos'] = $this->bets->getNumberOfPlayedOnesByUserAndPhase($user['userID'], $phaseID);
                $usersView[$k++] = array(
                    'RANK' => $i,
                    'ID' => $user['userID'],
                    'NAME' => $user['name'],
                    'LOGIN' => $user['login'],
                    'VIEW_BETS' => "<a href=\"/?op=edit_pronos&user=" . $user['userID'] . "\">",
                    'NBSCORES' => $user['nbscores'],
                    'NBPRONOS' => $user['nbpronos'],
                    'SCORERATE' => round(($user['nbscores'] / $user['nbpronos']) * 100, 1),
                    'TEAM' => $user['team']
                );
                $last_user = $user;
                $j++;

                $this->template->assign_block_vars('users', $usersView[$k - 1]);
            }
        }

        $this->template->assign_vars(array(
            'PHASE_NAME' => $phaseName,
            'PREVIOUS_PHASE_VISIBILITY' => ($phasePrecedente == NULL) ? "hidden" : "visible",
            'PREVIOUS_PHASE_ID' => ($phasePrecedente == NULL) ? $phaseID : $phasePrecedente['phaseID'],
            'NEXT_PHASE_VISIBILITY' => ($phaseSuivante == NULL) ? "hidden" : "visible",
            'NEXT_PHASE_ID' => ($phaseSuivante == NULL) ? $phaseID : $phaseSuivante['phaseID']
        ));

        $this->blocks_loaded[] = 'ranking_phase_perfect';
    }

    function loadResults($phaseID) {
        $this->template->set_filenames(array('view_results' => 'view_results.tpl'));

        // phases
        $phaseConsultee = $this->phases->getById($phaseID);
        $phases = $this->phases->get('DESC');
        foreach ($phases as $phase) {
            $this->template->assign_block_vars('phases', array(
                'ID' => $phase['phaseID'],
                'NAME' => $phase['name'],
                'SELECTED' => ($phaseConsultee['phaseID'] == $phase['phaseID']) ? ' selected="selected"' : ''
            ));
        }

        // get previous and next phases
        $phaseSuivante = $this->phases->getByDirectRoot($phaseID);
        $phasePrecedente = NULL;
        if ($phaseConsultee['phasePrecedente'] != NULL) {
            $phasePrecedente = $this->phases->getById($phaseConsultee['phasePrecedente']);
        }

        // results
        $results = $this->games->getResultsByPhase($phaseConsultee['phaseID'], false);
        $lastDate = '';
        foreach ($results as $result) {
            $dateDisplay = '';
            if ($lastDate != $result['dateStr']) {
                $dateDisplay = '<tr><td colspan="2"></td><td colspan="3" style="text-align:center;"><br /><i>' . $result['dateStr'] . '</i></td><td></td></tr>';
            }
            $lastDate = $result['dateStr'];
            $odds = $this->bets->getOddsByGame($result['matchID']);

            $imageMatch = "";
            if ($result['SPECIAL'] == 1) {
                $imageMatch = "<img src=\"" . $this->template_web_location . "/images/" . $this->config['bonus_game_img'] . "\" alt=\"(bonus)\" />";
            } elseif ($result['SPECIAL'] == 2) {
                $imageMatch = "<img src=\"" . $this->template_web_location . "/images/" . $this->config['lcp_game_img'] . "\" alt=\"(LCP)\" />";
            }

            $this->template->assign_block_vars('results', array(
                'MATCH_ID' => $result['matchID'],
                'DATE' => $dateDisplay,
                'IMG' => $imageMatch,
                'SCORE_MATCH_A' => $result['scoreMatchA'],
                'SCORE_MATCH_B' => $result['scoreMatchB'],
                'TEAM_NAME_A' => $result['teamAname'],
                'TEAM_NAME_B' => $result['teamBname'],
                'TEAM_IMG_A' => $this->template_web_location . '/images/fanions/' . formatImageFilename($result['teamAname']) . '.png',
                'TEAM_IMG_B' => $this->template_web_location . '/images/fanions/' . formatImageFilename($result['teamBname']) . '.png',
                'CLASS_A' => $result['CLASS_A'],
                'CLASS_B' => $result['CLASS_B'],
                'A_AVG' => $odds['A_AVG'],
                'B_AVG' => $odds['B_AVG'],
                'A_WINS' => $odds['A_WINS'],
                'NUL' => $odds['NUL'],
                'B_WINS' => $odds['B_WINS'],
                'NB_EXACT_BETS' => $odds['NB_EXACT_BETS'],
                'EXACT_BETS' => $odds['EXACT_BETS'],
                'NB_GOOD_BETS' => $odds['NB_GOOD_BETS'],
                'GOOD_BETS' => $odds['GOOD_BETS']
            ));
        }

        // ranking teams
        $matchs = $this->games->getUntilPhase($phaseConsultee['phaseID']);
        $teams = $this->teams->get();
        $ranked_teams = $this->teams->getRanking($teams, $matchs, 'scoreMatch', $_SESSION['userID']);
        foreach ($ranked_teams as $team) {
            $this->template->assign_block_vars('teams', array(
                'ID' => $team['teamID'],
                'NAME' => $team['name'],
                'IMG' => $this->template_web_location . '/images/fanions/' . formatImageFilename($team['name']) . '.png',
                'POINTS' => $team['points'],
                'DIFF' => $team['diff'],
                'STYLE' => isset($team['style']) ? ' style="' . $team['style'] . '"' : ''
            ));
        }

        // global vars
        $this->template->assign_vars(array(
            'PHASE_NAME' => $phaseConsultee['name'],
            'PREVIOUS_PHASE_VISIBILITY' => ($phasePrecedente == NULL) ? "hidden" : "visible",
            'PREVIOUS_PHASE_ID' => ($phasePrecedente == NULL) ? $phaseID : $phasePrecedente['phaseID'],
            'NEXT_PHASE_VISIBILITY' => ($phaseSuivante == NULL) ? "hidden" : "visible",
            'NEXT_PHASE_ID' => ($phaseSuivante == NULL) ? $phaseID : $phaseSuivante['phaseID'],
            'COMPETITION_NAME' => $this->lang['COMPETITION_NAME']
        ));

        $this->blocks_loaded[] = 'view_results';
    }

    function loadBets($userID, $phaseID, $mode) {
        $this->template->set_filenames(array('edit_bets' => 'edit_bets.tpl'));

        // phases
        $phases = $this->phases->get('DESC');
        foreach ($phases as $phase) {
            $this->template->assign_block_vars('phases', array(
                'ID' => $phase['phaseID'],
                'NAME' => $phase['name'],
                'SELECTED' => ($phaseID == $phase['phaseID']) ? ' selected="selected"' : ''
            ));
        }

        // get previous and next phases
        $phaseConsultee = $this->phases->getById($phaseID);
        $phaseSuivante = $this->phases->getByDirectRoot($phaseID);
        $phasePrecedente = NULL;
        if ($phaseConsultee['phasePrecedente'] != NULL) {
            $phasePrecedente = $this->phases->getById($phaseConsultee['phasePrecedente']);
        }

        // bets
        $bets = $this->bets->getByUser($userID, $phaseID, $mode);
        $lastDate = '';
        foreach ($bets as $bet) {
            $dateDisplay = '';
            if ($lastDate != $bet['dateStr']) {
                $dateDisplay = '<tr><td colspan="6" style="text-align:center;"><br /><i>' . $bet['dateStr'] . '</i></td></tr>';
            }
            $lastDate = $bet['dateStr'];

            $imageMatch = "";
            if ($bet['SPECIAL'] == 1)
                $imageMatch = "<img src=\"" . $this->template_web_location . "/images/" . $this->config['bonus_game_img'] . "\" alt=\"(bonus)\" />";
            elseif ($bet['SPECIAL'] == 2)
                $imageMatch = "<img src=\"" . $this->template_web_location . "/images/" . $this->config['lcp_game_img'] . "\" alt=\"(LCP)\" />";

            $this->template->assign_block_vars('bets', array(
                'MATCH_ID' => $bet['matchID'],
                'DATE' => $dateDisplay,
                'IMG' => $imageMatch,
                'SCORE_MATCH_A' => $bet['scoreMatchA'],
                'SCORE_MATCH_B' => $bet['scoreMatchB'],
                'SCORE_BET_A' => $bet['scorePronoA'],
                'SCORE_BET_B' => $bet['scorePronoB'],
                'TEAM_NAME_A' => $bet['teamAname'],
                'TEAM_NAME_B' => $bet['teamBname'],
                'TEAM_IMG_A' => $this->template_web_location . '/images/fanions/' . formatImageFilename($bet['teamAname']) . '.png',
                'TEAM_IMG_B' => $this->template_web_location . '/images/fanions/' . formatImageFilename($bet['teamBname']) . '.png',
                'COLOR' => $bet['COLOR'],
                'CLASS_A' => $bet['CLASS_A'],
                'CLASS_B' => $bet['CLASS_B'],
                'POINTS' => $bet['POINTS'],
                'DIFF' => $bet['DIFF'],
                'DISABLED' => $bet['DISABLED']
            ));
        }

        // ranking teams
        $pronos = $this->bets->getByUserUntilPhase($userID, $phaseID);
        $teams = $this->teams->get();
        $ranked_teams = $this->teams->getMixedRanking($teams, $pronos, $phaseID, $userID);
        foreach ($ranked_teams as $team) {
            $this->template->assign_block_vars('teams', array(
                'ID' => $team['teamID'],
                'NAME' => $team['name'],
                'IMG' => $this->template_web_location . '/images/fanions/' . formatImageFilename($team['name']) . '.png',
                'POINTS' => $team['points'],
                'DIFF' => $team['diff'],
                'STYLE' => isset($team['style']) ? ' style="' . $team['style'] . '"' : ''
            ));
        }

        // global vars
        $user = $this->users->getById($userID);
        $this->template->assign_vars(array(
            'COMPETITION_NAME' => $this->lang['COMPETITION_NAME'],
            'PREVIOUS_PHASE_VISIBILITY' => ($phasePrecedente == NULL) ? "hidden" : "visible",
            'PREVIOUS_PHASE_ID' => ($phasePrecedente == NULL) ? $phaseID : $phasePrecedente['phaseID'],
            'NEXT_PHASE_VISIBILITY' => ($phaseSuivante == NULL) ? "hidden" : "visible",
            'NEXT_PHASE_ID' => ($phaseSuivante == NULL) ? $phaseID : $phaseSuivante['phaseID'],
            'USER_ID' => $user['userID'],
            'USER_LOGIN' => $user['login'],
            'SUBMIT' => ($mode != 1) ? '<center><input type="image" src="' . $this->template_web_location . '/images/submit.gif" name="iptSubmit" /></center>' : ''
        ));

        $this->blocks_loaded[] = 'edit_bets';
    }

    function loadRules() {
        $this->template->set_filenames(array('rules' => 'rules.tpl'));

        $phase = $this->phases->getById(PHASE_ID_ACTIVE);
        $nbMatchsRegular = $phase['nb_matchs'] - 1;
        $this->template->assign_vars(array(
            'NB_PTS_RESULTAT' => $phase['nbPointsRes'],
            'NB_PTS_SCORE' => $phase['nbPointsScore'],
            'NB_TOTAL_MATCH' => $phase['nbPointsRes'] + $phase['nbPointsScore'],
            'NB_MATCHS_REGULAR' => $nbMatchsRegular,
            'TOTAL_PTS' => $nbMatchsRegular * ($phase['nbPointsRes'] + $phase['nbPointsScore']),
            'NB_PTS_RESULTAT_BONUS' => $phase['nbPointsRes'] * $phase['multiplicateurMatchDuJour'],
            'NB_PTS_SCORE_BONUS' => $phase['nbPointsScore'] * $phase['multiplicateurMatchDuJour'],
            'NB_TOTAL_MATCH_BONUS' => ($phase['nbPointsRes'] + $phase['nbPointsScore']) * $phase['multiplicateurMatchDuJour'],
            'TOTAL_PTS_BONUS' => ($phase['nbPointsRes'] + $phase['nbPointsScore']) * $phase['multiplicateurMatchDuJour'],
            'TOTAL_PHASE' => (($phase['nbPointsRes'] + $phase['nbPointsScore']) * $phase['multiplicateurMatchDuJour']) + ($nbMatchsRegular * ($phase['nbPointsRes'] + $phase['nbPointsScore']))
        ));

        $this->blocks_loaded[] = 'rules';
    }

    function loadHeader($connected = false) {
        $this->template->set_filenames(array(
            'header' => 'header.tpl'
        ));

        if ($connected) {
            $this->template->assign_block_vars('load_infos', array());
        }

        $this->template->assign_vars(array(
            'TITLE' => $this->config['title'],
            'URL' => $this->config['url'],
            'LOGOUT_LINK' => $connected ? '<br /><a href="/?op=logout" class="logout">Déconnexion</a>' : '',
            'LOGO' => $this->config['logo'],
            'TPL_WEB_PATH' => $this->template_web_location
        ));

        $this->blocks_loaded[] = 'header';
    }

    function loadMenu() {
        $this->template->set_filenames(array(
            'menu' => 'menu.tpl'
        ));

        if ($this->isLogged()) {
            $this->template->assign_block_vars('user_bar', array());
            if ($this->isAdmin()) {
                $this->template->assign_block_vars('admin_bar', array());
            }
        }

        $this->blocks_loaded[] = 'menu';
    }

    function loadFooter($private = false) {
        if ($private) {
            $this->template->set_filenames(array('footer' => 'footer_private.tpl'));
        } else {
            $this->template->set_filenames(array('footer' => 'footer_public.tpl'));
        }
        $this->template->assign_vars(array(
            'TPL_WEB_PATH' => $this->template_web_location,
            'FORUM_LINK' => $this->config['forum_link'],
            'CONTACT_EMAIL' => $this->config['support_email']
        ));

        $this->blocks_loaded[] = 'footer';
    }

    function loadInfos($userID) {
        $this->template->set_filenames(array('infos' => 'infos.tpl'));

        // Next games
        $nextGames = $this->games->getNextOnes();
        $nbGames = sizeof($nextGames);
        if ($nbGames > 0) {
            $this->template->assign_block_vars('games_infos', array());
            foreach ($nextGames as $game) {
                $delay_str = format_delay($game['delay_sec']);
                $this->template->assign_block_vars('games_infos.games', array(
                    'TEAM_A' => $game['teamAname'],
                    'TEAM_B' => $game['teamBname'],
                    'DATE' => $delay_str
                ));
            }
        }

        // Next bets
        $bets = $this->bets->getNextOnesByUser($userID, $this->phases->getNextPhaseIdToBet());
        $nbBets = sizeof($bets);
        if ($nbBets > 0) {
            $this->template->assign_block_vars('bets_infos', array(
                'NB_BETS' => $nbBets,
                'TOO_MUCH_BETS' => (sizeof($bets) > 3) ? '...' : ''
            ));

            if ($nbBets > 3)
                $nbBets = 2;
            for ($i = 0; $i < $nbBets; $i++) {
                $delay_str = format_delay($bets[$i]['delay_sec']);
                $this->template->assign_block_vars('bets_infos.bets', array(
                    'TEAM_A' => $bets[$i]['teamAname'],
                    'TEAM_B' => $bets[$i]['teamBname'],
                    'DATE' => $delay_str
                ));
            }
            if (sizeof($bets) > 2) {
                $this->template->assign_block_vars('bets', array());
            }
        }

        $this->blocks_loaded[] = 'infos';
        $this->display();
    }

    function loadRegister($warning) {
        $this->template->set_filenames(array('register' => 'register.tpl'));

        $this->template->assign_vars(array(
            'TPL_WEB_PATH' => $this->template_web_location,
            'WARNING' => $warning
        ));

        $this->blocks_loaded[] = 'register';
    }

    function loadUsers() {
        $this->template->set_filenames(array('users' => 'users.tpl'));

        // user & teams
        $teams = $this->users->getTeams();
        $teams[] = array('userTeamID' => 'NULL', 'name' => 'Sans équipe');
        foreach ($teams as $team) {
            $this->template->assign_block_vars('teams', array(
                'ID' => $team['userTeamID'],
                'NAME' => $team['name']
            ));

            $users = $this->users->getByTeam($team['userTeamID'], 'all');
            foreach ($users as $user) {
                $this->template->assign_block_vars('teams.users', array(
                    'ID' => $user['userID'],
                    'NAME' => $user['name'],
                    'LOGIN' => $user['login']
                ));
            }
        }



        $this->blocks_loaded[] = 'users';
    }

    function loadEditResults() {
        $this->template->set_filenames(array('edit_results' => 'edit_results.tpl'));

        // games
        $games = $this->games->get();
        $lastDate = "";
        foreach ($games as $game) {
            $this->template->assign_block_vars('games', array(
                'ID' => $game['matchID'],
                'TEAM_NAME_A' => $game['teamAname'],
                'TEAM_NAME_B' => $game['teamBname'],
                'TEAM_IMG_A' => formatImageFilename($game['teamAname']),
                'TEAM_IMG_B' => formatImageFilename($game['teamBname']),
                'SCORE_A' => $game['scoreMatchA'],
                'SCORE_B' => $game['scoreMatchB'],
                'COLOR_A' => $game['COLOR_A'],
                'COLOR_B' => $game['COLOR_B']
            ));

            // game passed ?
            if ($lastDate != $game['dateStr']) {
                $this->template->assign_block_vars('games.date', array(
                    'LABEL' => $game['dateStr'],
                ));
            }

            $lastDate = $game['dateStr'];
        }

        $this->template->assign_vars(array(
            'TPL_WEB_PATH' => $this->template_web_location
        ));

        $this->blocks_loaded[] = 'edit_results';
    }

    function loadGames() {
        $this->template->set_filenames(array('edit_games' => 'edit_games.tpl'));

        $dateCourante = getdate();

        // days
        for ($jour = 1; $jour <= 31; $jour++) {
            $this->template->assign_block_vars('days', array(
                'VALUE' => $jour,
                'SELECTED' => ($jour == $dateCourante['mday']) ? 'selected="selected"' : ''
            ));
        }

        // months
        $months = $this->settings->getMonths();
        foreach ($months as $month) {
            $this->template->assign_block_vars('months', array(
                'VALUE' => $month[0],
                'LABEL' => $month[1],
                'SELECTED' => ($month[0] == $dateCourante['mon']) ? 'selected="selected"' : ''
            ));
        }

        // years
        $years = $this->settings->getYears();
        foreach ($years as $year) {
            $this->template->assign_block_vars('years', array(
                'VALUE' => $year,
                'SELECTED' => ($year == $dateCourante['year']) ? 'selected="selected"' : ''
            ));
        }

        // phases
        $phases = $this->phases->get('DESC');
        $phaseToFill = $this->phases->getFirstToFill();
        if (!isset($phaseToFill['phaseID'])) {
            $phaseToFill['phaseID'] = -1;
        }
        foreach ($phases as $phase) {
            $this->template->assign_block_vars('phases', array(
                'ID' => $phase['phaseID'],
                'NAME' => $phase['name'],
                'SELECTED' => ($phase['phaseID'] == $phaseToFill['phaseID']) ? 'selected="selected"' : ''
            ));

            $games = $this->games->getByPhase($phase['phaseID']);
            foreach ($games as $game) {
                $imageMatch = "";
                if ($game['status'] == 1) {
                    $imageMatch = "<img src=\"" . $this->template_web_location . "/images/" . $this->config['bonus_game_img'] . "\" height=\"15px\" alt=\"(bonus)\" />";
                } elseif ($game['status'] == 2) {
                    $imageMatch = "<img src=\"" . $this->template_web_location . "/images/" . $this->config['lcp_game_img'] . "\" height=\"15px\" alt=\"(LCP)\" />";
                }

                $this->template->assign_block_vars('phases.games', array(
                    'ID' => $game['matchID'],
                    'DATE' => $game['dateStr'],
                    'TEAM_NAME_A' => $game['teamAname'],
                    'TEAM_NAME_B' => $game['teamBname'],
                    'IMG' => $imageMatch
                ));
            }
        }

        // teams
        $teams = $this->teams->get();
        foreach ($teams as $team) {
            $this->template->assign_block_vars('teams', array(
                'ID' => $team['teamID'],
                'NAME' => $team['name']
            ));
        }

        $this->template->assign_vars(array(
            'TPL_WEB_PATH' => $this->template_web_location,
            'LCP' => $this->config['lcp_short_label']
        ));

        $this->blocks_loaded[] = 'edit_games';
    }

    function sendIDs($email) {
        if ($email) {
            $user = $this->users->getByEmail($email);
            if ($user) {
                if ($newPassword = $this->users->setNewPassword($user['userID'])) {
                    $res = utf8_mail($user['email'], "Pronos L1 - Rappel de vos identifiants", "Bonjour,\n\nVotre login est : " . $user['login'] . "\nVotre nouveau mot de passe est : " . $newPassword . "\n\nCordialement,\nL'équipe " . $this->config['support_team'] . "\n", $this->config['title'], $this->config['support_email'], $this->config['email_simulation']);
                } else {
                    $res = false;
                }

                if (!$res) {
                    utf8_mail($this->config['email'], "Pronos L1 - Problème envoi à '" . $email . "'", "L'utilisateur avec l'email '" . $email . "' a tenté de récupérer ses identifiants.\n", $this->config['title'], $this->config['support_email'], $this->config['email_simulation']);
                    return FORGOT_IDS_KO;
                } else {
                    return FORGOT_IDS_OK;
                }
            } else {
                utf8_mail($this->config['email'], "Pronos L1 - Email '" . $email . "' inconnu", "L'utilisateur avec l'email '" . $email . "' a tenté de récupérer ses identifiants.\n", $this->config['title'], $this->config['support_email'], $this->config['email_simulation']);
                return EMAIL_UNKNOWN;
            }
        } else {
            return INCORRECT_EMAIL;
        }
    }

}
?>
