<?php

class Tags {

    var $parent;
    var $max_results = 5000;

    function Tags(&$parent) {
        $this->parent = $parent;
    }

    function get($start = false, $limit = false, $userTeamId = false) {
        // Main Query
        $req = "SELECT *, DATE_FORMAT(date,'%d/%m %kh%i') as date_str";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "tags t ";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "users u ON (u.userID = t.userID)";
        $req .= " WHERE t.instanceID = " . $this->parent->config['current_instance'];
        if ($userTeamId)
            $req .= " AND t.userTeamID = " . $userTeamId;
        else
            $req .= " AND t.userTeamID = -1";
        $req .= " ORDER BY date DESC";
        if ($limit != false)
            $req .= " LIMIT " . $start . "," . $limit . "";

        $tags = $this->parent->db->select_array($req, $this->max_results);

        if ($this->parent->debug)
            array_show($tags);

        return $tags;
    }

    function getNumberOf($userTeamID = -1) {
        // Main Query
        $req = "SELECT count(*)";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "tags t ";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "users u ON (u.userID = t.userID)";
        $req .= " WHERE t.userTeamID = " . $userTeamID;
        $req .= " AND t.instanceID = " . $this->parent->config['current_instance'];

        $nb_tags = $this->parent->db->select_one($req);

        if ($this->parent->debug)
            echo($nb_tags);

        return $nb_tags;
    }

    function getById($tagID) {
        // Main Query
        $req = "SELECT *, DATE_FORMAT(date,'%d/%m %kh%i') as date_str";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "tags t ";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "users u ON (u.userID = t.userID)";
        $req .= " WHERE tagID = " . $tagID;

        $tag = $this->parent->db->select_line($req, $this->max_results);

        if ($this->parent->debug)
            array_show($tag);

        return $tag;
    }

    function save($text, $userTeamID = false) {
        $userID = $_SESSION['userID'];
        if (!$userTeamID) {
            $userTeamID = -1;
        }
        prepare_numeric_data(array(&$userTeamID, &$userID));
        prepare_alphanumeric_data(array(&$text));
        $text = htmlspecialchars(trim($text));
        $tagID = $this->parent->db->insert("INSERT INTO  " . $this->parent->config['db_prefix'] . "tags (userID, userTeamID, date, tag, instanceID) VALUES (" . $userID . ", " . $userTeamID . ", NOW(), '" . $text . "', " . $this->parent->config['current_instance'] . ")");

        return;
    }

    function delete($tagID) {
        $tag = $this->getById($tagID);
        if (($tag['userID'] != $_SESSION['userID']) && !$this->parent->isAdmin()) {
            return;
        }

        $tagID = $this->parent->db->exec_query("DELETE FROM " . $this->parent->config['db_prefix'] . "tags WHERE tagID = " . $tagID . "");

        return;
    }
}
