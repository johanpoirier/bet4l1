<?
class Instances {

    var $parent;
    var $max_results = 500;

    function Instances(&$parent) {
        $this->parent = $parent;
    }

    function getById($id) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "instances";
        $req .= " WHERE id = " . $id;

        $instance = $this->parent->db->select_line($req, $this->max_results);

        return $instance;
    }

    function getByName($name) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "instances";
        $req .= " WHERE name = '" . $name . "'";

        $instance = $this->parent->db->select_line($req, $this->max_results);

        return $instance;
    }

    function get() {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "instances";
        $req .= " ORDER BY name ASC";

        $instances = $this->parent->db->select_array($req, $this->max_results);

        return $instances;
    }

    function getWithDetails() {
        // Main Query
        $req = "SELECT i.id, i.name, i.ownerID, i.parentID, i.active, COUNT(p.phaseID) as nbPhases";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "instances as i";
        $req .= " LEFT JOIN " . $this->parent->config['db_prefix'] . "phases as p ON(i.id = p.instanceID)";
        $req .= " GROUP BY i.id";
        $req .= " ORDER BY name ASC";

        $instances = $this->parent->db->select_array($req, $this->max_results);

        return $instances;
    }

    function getActiveOnes() {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "instances";
        $req .= " WHERE active = 1";

        $instances = $this->parent->db->select_array($req, $this->max_results);

        return $instances;
    }

    function update($id, $name, $active) {
        $req = "UPDATE " . $this->parent->config['db_prefix'] . "instances SET";
        $req .= " name = '" . $name . "'";
        $req .= ", active = " . $active;
        $req .= " WHERE id = " . $id;

        return $this->parent->db->exec_query($req);
    }

    function add($name, $ownerID, $parentId, $copyData) {
        $req = "INSERT INTO " . $this->parent->config['db_prefix'] . "instances (name, ownerID, parentID, active)";
        $req .= " VALUES ('" . addslashes($name) . "', " . $ownerID . ", " . $parentId . ", 0)";
        $ret = $this->parent->db->insert($req);

        $newInstance = $this->getByName($name);
        if($copyData == 1 && $parentId != 0) {
            // user teams
            $user_teams = $this->parent->users->getTeamsByInstance($parentId);
            foreach ($user_teams as $team) {
                $this->parent->users->addTeam($team['name'], $newInstance['id']);
            }

            // users
            $users = $this->parent->users->get($parentId);
            foreach ($users as $user) {
                $user_team = $this->parent->users->getTeamByNameAndInstance($user['team'], $parentId);
                $this->parent->users->add($user['login'], $user['password'], $user['name'], "", $user['email'], $user_team ? $user_team['userTeamID'] : 'NULL', $user['status'], $newInstance['id'], true);
            }

            // settings
            $settings = $this->parent->settings->getByInstance($parentId);
            foreach ($settings as $setting) {
                $this->parent->settings->add($newInstance['id'], $setting['name'], $setting['value'], $setting['date']);
            }

            // teams
            $teams = $this->parent->teams->get($parentId);
            foreach ($teams as $team) {
                $this->parent->teams->add($team['name'], $team['rssName'], $newInstance['id']);
            }
        }

        return $ret;
    }

    function delete($id) {
        $req = "DELETE";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "instances";
        $req .= " WHERE id = " . $id;

        // TODO : Delete all datas related to the instance

        // not ui safe yet
        //$this->parent->db->exec_query($req);

        return;
    }
}
?>