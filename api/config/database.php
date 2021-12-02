<?php
// used to get mysql database connection
class Database{
    // specify your own database credentials
    private $host = "localhost";
    private $port = "3392"; //work
    //private $port = "3306"; //home
    private $db_name = "goods";
    private $username = "root";
    private $password = "nong420631";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=$this->host:$this->port;dbname=$this->db_name", "$this->username", "$this->password");

        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        } 

        return $this->conn;
    }
}
?>