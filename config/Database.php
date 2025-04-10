<?php
//config//database.php
class Database {
private $host = 'localhost';
private $username = 'root';
private $password = '';
private $database = 'simple_crud';
private $conn;

public function getConnection() {
    $this->conn = null;

    try {   
            $this->conn = new mysqli($this->host,$this->username,$this->password,$this->database);
            if($this->conn->connect_error){
            throw new exception("connection failed".$this->conn->connect_error);    
            }
        }
    catch(Exception $e){
        echo "Connection error".$e->getMessage();
    }
    return $this->conn;
    }
}

