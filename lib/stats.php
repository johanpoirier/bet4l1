<?php

class Stats
{
    var $parent;

    function Stats(&$parent)
    {
        $this->parent = $parent;
    }

    function getUserStats($userID)
    {
        $req = "SELECT *, label FROM " . $this->parent->config['db_prefix'] . "stats_user";
        $req .= " WHERE userID = " . $userID;

        $stats = $this->parent->db->select_array($req, $nb_stats);

        return $stats;
    }

    function getUserStatsMaxOf($data)
    {
        $req = "SELECT MAX($data) FROM " . $this->parent->config['db_prefix'] . "stats_user";
        $max = $this->parent->db->select_one($req);

        return $max;
    }

    function createStats()
    {
        $users = $this->loadRanking();

        $reqBase = "INSERT INTO  " . $this->parent->config['db_prefix'] . "stats_user VALUES";
        foreach ($users as $user) {
            $req = $reqBase . " (" . $user['ID'] . ", NOW(), " . $user['RANK'] . ", " . $user['POINTS'] . ", " . $user['NBRESULTS'] . ", " . $user['NBSCORES'] . ", " . $user['DIFF'] . ")";
            $this->parent->db->insert($req);
        }
        echo "OK";

        return;
    }

    function regenerateStats()
    {
        // empty the table
        $this->parent->db->exec_query("DELETE FROM " . $this->parent->config['db_prefix'] . "stats_user");

        // ranking snapshot for each phase
        $reqBase = "INSERT INTO  " . $this->parent->config['db_prefix'] . "stats_user VALUES";
        $phases = $this->parent->phases->getPlayedOnes('ASC');
        $globalUsersRanking = array();
        foreach ($phases as $phase) {
            $users = $this->parent->users->getRankingByPhase($phase['phaseID']);
            $globalUsersRanking = $this->addPhaseRanksToGlobalRanking($globalUsersRanking, $users);
            $rankedUsers = $globalUsersRanking;
            usort($rankedUsers, "compare_users");
            $rank = 0;
            foreach ($rankedUsers as $user) {
                $rank++;
                $req = $reqBase . " (" . $user['userID'] . ", '" . $phase['name'] . "', $rank, " . $user['points'] . ", " . $user['nbresults'] . ", " . $user['nbscores'] . ", " . $user['diff'] . ")";
                $this->parent->db->insert($req);
            }
        }

        return;
    }

    function addPhaseRanksToGlobalRanking($usersRanking, $ranksToAdd)
    {
        if (sizeof($usersRanking) == 0) {
            $usersRanking = $ranksToAdd;
        } else {
            foreach ($ranksToAdd as $rank) {
                $id = $rank['userID'];
                if (!isset($usersRanking[$id])) {
                    $usersRanking[$id] = [
                        'userID' => $id,
                        'points' => 0,
                        'nbresults' => 0,
                        'nbscores' => 0,
                        'diff' => 0
                    ];
                }
                $usersRanking[$id]['points'] += $rank['points'];
                $usersRanking[$id]['nbresults'] += $rank['nbresults'];
                $usersRanking[$id]['nbscores'] += $rank['nbscores'];
                $usersRanking[$id]['diff'] += $rank['diff'];
            }
        }
        return $usersRanking;
    }
}
