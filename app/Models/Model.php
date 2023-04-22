<?php

$path = explode("\\", dirname(__FILE__));
$path = implode("/", array_slice($path, 0, count($path) - 2));

require_once $path . "/config/database.php";

/**
 * Description of Model
 *
 * @author Maurcio Herrera
 */
class Model {

    protected $db_host = DB_HOST;
    protected $db_user = DB_USER;
    protected $db_pass = DB_PASS;
    protected $db_name = DB_NAME;
    protected $conecction;
    protected $query;
    protected $table;
    protected $model;
    protected $rs;

    public function __construct() {
//        $this->desconectar();
        $this->conection();
    }

    public function conection() {
        try {
            $this->conecction = new PDO($this->db_host . ";" . "dbname=" . $this->db_name . ";charset=utf8", $this->db_user, $this->db_pass,
                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->conecction->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conecction->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET SESSION wait_timeout=120");
            $this->conecction->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET SESSION max_allowed_packet=25M");
        } catch (Exception $e) {
            echo json_encode($e->getMessage());
            die();
        }
    }

    public function desconectar() {
        $this->query = null;
        $this->conecction = null;
    }

    public function query($sql) {
        $this->query = $this->conecction->prepare($sql);
        $this->query->execute();
        return $this;
    }

    public function exec($query) {
        $this->query = $this->conecction->prepare($query);
        $this->query->execute();
        return $this->query->rowCount();
    }

    public function first() {
        return $this->query->fetch(PDO::FETCH_OBJ);
    }

    public function get() {
        return $this->query->fetchAll(PDO::FETCH_OBJ);
    }

    //Consultas
    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->query($sql)->get();
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        return $this->query($sql)->first();
    }

    public function where($column, $operator, $value = null) {
        if ($value == null) {
            $value = $operator;
            $operator = '=';
        }
        $value = $this->my_escape_string($value);
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} '{$value}'";
        $this->query($sql);
        return $this;
    }

    public function create($data) {
        //Columnas
        $columns = array_keys($data);
        $columns = implode(", ", $columns);
        //Valores
        $values = array_values($data);
        $values = "'" . implode("', '", $values) . "'";
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
//        echo $sql;
        $this->query($sql);
        $id = $this->conecction->lastInsertId();
        if (!empty($id)) {
            return $this->find($id);
        } else {
            return $this->find($data["id"]);
        }
    }

    public function update($data, $id) {
        //Columnas
        $pdata = array();
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $pdata[] = $key . "='" . $value . "'";
            } else {
                $pdata[] = $key . "=null" ;
            }
        }
        $pdata = "SET " . implode(", ", $pdata);
        $sql = "UPDATE {$this->table} {$pdata} WHERE id = {$id}";   
        $this->query($sql);
        return $this->find($id);
    }

    public function delete($field, $value) {
        $sql = "DELETE FROM {$this->table} WHERE {$field} = '{$value}'";

        $this->query = $this->conecction->prepare($sql);
        $this->query->execute();
        return $this->query->rowCount();
    }

    function my_escape_string($string) {
        $search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a");
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z");

        return str_replace($search, $replace, $string);
    }

}
