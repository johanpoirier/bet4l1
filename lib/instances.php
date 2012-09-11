<?
class Instances {

    var $parent;

    function Instances(&$parent) {
        $this->parent = $parent;
    }

    function getById($id) {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "instances";
        $req .= " WHERE id = " . $id;

        $instance = $this->parent->db->select_line($req, $nb_lines);

        return $instance;
    }

    function getActiveOnes() {
        // Main Query
        $req = "SELECT *";
        $req .= " FROM " . $this->parent->config['db_prefix'] . "instances";
        $req .= " WHERE active = 1";

        $instances = $this->parent->db->select_array($req, $nb_teams);

        return $instances;
    }
}
?>