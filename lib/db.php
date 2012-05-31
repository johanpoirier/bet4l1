<?

include(BASE_PATH . 'lib/protect/params.inc');

class DB {

    var $cnx;
    var $error;
    var $nbQueries;
    var $DBTime;
    var $host;
    var $dbname;
    var $username;
    var $password;

    function DB($host="localhost", $dbname="", $username="", $password="") {
        global $host;
        global $dbname;
        global $username;
        global $password;

        $this->cnx = false;
        $this->error = true;
        $this->nb_queries = 0;
        $this->exec_time = 0;
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->debug = false;
    }

    function set_debug($debug) {
        $this->debug = $debug;
    }

    function exec_query($req) {
        // Start Time
        $startTime = get_moment();
        $this->nb_queries++;

        if ($this->debug)
            echo "REQUEST N°" . $this->nb_queries . "='" . $req . "'";

        if (!$this->cnx)
            $this->cnx = @mysql_connect($this->host, $this->username, $this->password);
        if (!$this->cnx)
            return $this->error_query("Echec Connexion MySql", $this->cnx);

        mysql_query("SET NAMES UTF8");
        mysql_query("SET SQL_MODE='ANSI_QUOTES'");
        mysql_query("SET lc_time_names = 'fr_FR'");

        $ret_db = mysql_select_db($this->dbname, $this->cnx);
        if (!$ret_db)
            return $this->error_query(mysql_error($this->cnx), $req);

        $ret_id = mysql_query($req, $this->cnx);
        if (!$ret_id)
            return $this->error_query(mysql_error($this->cnx), $req);

        $elapsed_time = get_elapsed_time($startTime, get_moment());

        if ($this->debug)
            echo $elapsed_time . "s<br/>";

        $this->exec_time += $elapsed_time;

        return $ret_id;
    }

    // Fonctions de renvoi de resultset, en cas d'erreur : 1ere valeur = chr(31) + message d'erreur
    // Renvoie une unique valeur (ne renvoie que la 1ere valeur)
    function select_one($req) {
        $ret_id = $this->exec_query($req);
        if ($this->test_error($ret_id))
            return $ret_id;
        if (mysql_num_rows($ret_id)) {
            $resultset = mysql_fetch_array($ret_id);
            return $resultset [0];
        }
        return false;
    }

    // Renvoie une ligne (i.e. un enregistrement), ne renvoie que le 1er si plusieurs
    function select_line($req, &$NbLig) {
        $ret_id = $this->exec_query($req);
        if ($this->test_error($ret_id))
            return $ret_id;
        else {
            $NbLig = mysql_num_rows($ret_id);
            $resultset = $NbLig ? mysql_fetch_array($ret_id) : "";
        }
        return $resultset;
    }

    // Renvoie resultset complet
    function select_col($req, &$NbCol) {
        $ret_id = $this->exec_query($req);
        if ($this->test_error($ret_id))
            return $ret_id;
        else {
            $NbCol = mysql_num_rows($ret_id);
            for ($i = 0; $i < $NbCol; $i++) {
                $tmp_resultset = mysql_fetch_array($ret_id);
                $resultset[$i] = $tmp_resultset[0];
            }
        }
        return $resultset;
    }

    // Renvoie resultset complet
    function select_array($req, &$NbLig) {
        $ret_id = $this->exec_query($req);
        $resultset = array();
        if ($this->test_error($ret_id))
            return $ret_id;
        else {
            $NbLig = mysql_num_rows($ret_id);
            for ($i = 0; $i < $NbLig; $i++)
                $resultset[$i] = mysql_fetch_array($ret_id);
        }
        return $resultset;
    }

    // Renvoie une ligne (i.e. un enregistrement), ne renvoie que le 1er si plusieurs
    function select_line_o($req, &$NbLig) {
        $ret_id = $this->exec_query($req);
        if ($this->test_error($ret_id))
            return $ret_id;
        else {
            $NbLig = mysql_num_rows($ret_id);
            $resultset = $NbLig ? mysql_fetch_object($ret_id) : "";
        }
        return $resultset;
    }

    // Renvoie resultset complet
    function select_col_o($req, &$NbCol) {
        $ret_id = $this->exec_query($req);
        if ($this->test_error($ret_id))
            return $ret_id;
        else {
            $NbCol = mysql_num_rows($ret_id);
            for ($i = 0; $i < $NbCol; $i++) {
                $tmp_resultset = mysql_fetch_object($ret_id);
                $resultset[$i] = $tmp_resultset[0];
            }
        }
        return $resultset;
    }

    // Renvoie resultset complet
    function select_array_o($req, &$NbLig) {
        $ret_id = $this->exec_query($req);
        $resultset = array();
        if ($this->test_error($ret_id))
            return $ret_id;
        else {
            $NbLig = mysql_num_rows($ret_id);
            for ($i = 0; $i < $NbLig; $i++)
                $resultset[$i] = mysql_fetch_object($ret_id);
        }
        return $resultset;
    }

    // Fonction req sans retour
    function select_null($req) {
        return $this->exec_query($req);
    }

    // Insert returning
    function insert($req) {
        $tmp = explode(" ", $req);
        if (strtolower($tmp[0]) != "insert" || strtolower($tmp[1]) != "into") {
            return $this->exec_query("Insert appelé sur requête qui n'est pas un insert", $req);
        }
        $table = $tmp[2];
        $ret_id = $this->exec_query($req);
        if (substr($ret_id, 0, 1) == chr(31))
            return $ret_id;
        return mysql_insert_id();
    }

    // Procédure d'erreur
    function error_query($msg, $req) {
        if ($this->error)
            $this->display_error($msg, $req);
        if ($this->error != 2)
            return chr(31) . $msg;
        exit;
    }

    // note : substr (0,1) renvoie "A" pour Array si le resultat est un tableau (donc pas de risque d'avoir un chr31)
    function test_error($ret) {
        return substr(strval($ret), 0, 1) == chr(31) ? substr(strval($ret), 1) : 0;
    }

    // Envoi mail, écriture log, affichage...
    function display_error($msg, $req) {
        echo "<b><i>" . $msg . "</i></b>&nbsp";
        echo " généré par la requête : <i>\"" . $req . "\"</i><br>";
    }

}

?>
