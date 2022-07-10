<?php 
class Database {
    protected $hostName = 'localhost';
    protected $userName = 'root';
    protected $password = '';
    protected $dbName = 'crawl';
    protected $conn;
    function __construct(){
        try {
           $this->conn = new PDO("mysql:host=$this->hostName;dbname=$this->dbName", $this->userName,$this->password);
           $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $th) {
            echo "Connection fail". $th->getMessage();
        }
    }
}