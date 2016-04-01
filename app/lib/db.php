<?php

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

        if ($this->debug) {
            echo "REQUEST N°" . $this->nb_queries . "='" . $req . "'";
        }

        if (!$this->cnx) {
            $this->cnx = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname, $this->username, $this->password);
        }
        if (!$this->cnx) {
            return $this->error_query("Echec Connexion MySql", $this->cnx);
        }

        try {
            $result = $this->cnx->query($req, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return $e;
        }

        $elapsed_time = get_elapsed_time($startTime, get_moment());

        if ($this->debug) {
            echo $elapsed_time . "s<br/>";
        }

        $this->exec_time += $elapsed_time;

        return $result;
    }

    function select_one($req) {
        $statement = $this->exec_query($req);
        if ($this->test_error($statement)) {
            return $statement;
        }
        if ($statement->rowCount() > 0) {
            $result = $statement->fetch();
            return array_shift($result);
        }
        return false;
    }

    function select_line($req, &$nbLines) {
        $statement = $this->exec_query($req);
        if ($this->test_error($statement)) {
            return $statement;
        }
        $nbLines = $statement->rowCount();
        return $statement->fetch();
    }

    function select_col($req, &$nbCols) {
        $statement = $this->exec_query($req);
        $resultSet = [];
        if ($this->test_error($statement))
            return $statement;
        else {
            $nbCols = $statement->rowCount();
            for ($i = 0; $i < $nbCols; $i++) {
                $tmpResultSet = $statement->fetch();
                $resultSet[$i] = $tmpResultSet[0];
            }
        }
        return $resultSet;
    }

    function select_array($req, &$nbLines) {
        $statement = $this->exec_query($req);
        $resultSet = [];
        if ($this->test_error($statement)) {
            return $statement;
        }
        else {
            $nbLines = $statement->rowCount();
            for ($i = 0; $i < $nbLines; $i++) {
                $resultSet[$i] = $statement->fetch();
            }
        }
        return $resultSet;
    }

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
        return $this->exec_query($req);
    }

    // Procédure d'erreur
    function error_query($msg, $req) {
        if ($this->error)
            $this->display_error($msg, $req);
        if ($this->error != 2)
            return chr(31) . $msg;
        exit;
    }

    function test_error($result) {
        return $result === false;
    }

    // Envoi mail, écriture log, affichage...
    function display_error($msg, $req) {
        echo "<b><i>" . $msg . "</i></b>&nbsp";
        echo " généré par la requête : <i>\"" . $req . "\"</i><br>";
    }
}
