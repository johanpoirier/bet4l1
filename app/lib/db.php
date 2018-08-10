<?php

include(BASE_PATH . 'lib/protect/params.inc');

class MySQL_DB
{
    var $cnx;
    var $error;
    var $nbQueries;
    var $DBTime;
    var $host;
    var $port;
    var $dbname;
    var $username;
    var $password;

    public function __construct($host = "localhost", $dbname = "", $port = "", $username = "", $password = "")
    {
        global $host;
        global $dbname;
        global $port;
        global $username;
        global $password;

        $this->cnx = false;
        $this->error = true;
        $this->nb_queries = 0;
        $this->exec_time = 0;
        $this->host = $host;
        $this->port = $port;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->debug = false;
    }

    function set_debug($debug)
    {
        $this->debug = $debug;
    }

    function exec_query($req, $params = []) {
        // Start Time
        $startTime = get_moment();
        $this->nb_queries++;

        if ($this->debug) {
            echo "REQUEST N°" . $this->nb_queries . "='" . $req . "'";
        }

        if (!$this->cnx) {
            $this->cnx = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname, $this->username, $this->password, [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET lc_time_names='fr_FR',NAMES utf8"
            ]);
        }
        if (!$this->cnx) {
            return $this->error_query("Echec Connexion MySql", $this->cnx);
        }

        $this->cnx->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        try {
            $statement = $this->cnx->prepare($req);
            $statement->execute($params);
        } catch(PDOException $e) {
            return $e;
        }

        $elapsed_time = get_elapsed_time($startTime, get_moment());

        if ($this->debug) {
            echo $elapsed_time . 's<br/>';
        }

        $this->exec_time += $elapsed_time;

        return $statement;
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

    function selectLine($req, $params = [], &$nbLines) {
        $statement = $this->exec_query($req, $params);
        if ($this->test_error($statement)) {
            return $statement;
        }
        $nbLines = $statement->rowCount();
        return $statement->fetch(PDO::FETCH_ASSOC);
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
                $resultSet[$i] = array_shift($tmpResultSet);
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

    function selectArray($req, $params = [], &$nbLines) {
        $statement = $this->exec_query($req, $params);

        if ($this->test_error($statement)) {
            return $statement;
        }
        else {
            $nbLines = $statement->rowCount();
            $resultSet = $statement->fetchAll(PDO::FETCH_ASSOC);
        }

        return $resultSet;
    }

    // Fonction req sans retour
    function select_null($req)
    {
        return $this->exec_query($req);
    }

    // Insert returning
    function insert($req) {
        $this->exec_query($req);
        return $this->cnx->lastInsertId();
    }

    // Procédure d'erreur
    function error_query($msg, $req)
    {
        if ($this->error) {
            $this->display_error($msg, $req);
        }
        if ($this->error != 2) {
            return chr(31) . $msg;
        }
        exit;
    }

    function test_error($result)
    {
        return $result === false;
    }

    function display_error($msg, $req)
    {
        echo "<b><i>" . $msg . "</i></b>&nbsp";
        echo " généré par la requête : <i>\"" . $req . "\"</i><br>";
    }
}
