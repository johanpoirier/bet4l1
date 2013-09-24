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

    function add($name, $parentId, $copyData) {
        // TODO
    }
}
?>