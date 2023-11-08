<?php
//database config
class Connection {
    private static $db;
    public function __construct() {
        //local
        $this->db = new PDO('mysql:host=localhost;dbname=Incidents', 'root', '');
    }

    public function getConnection() {
        return $this->db;
    }
}
?>