<?php
// used to get mysql database connection
class Database{
    // specify your own database credentials
    private $host = "192.168.70.219";
    private $port = "3306";
    private $db_name = "goods";
    private $username = "administrator";
    private $password = "P@ssw0rd";

    // Test Database
    /*private $host = "192.168.50.230";
    private $port = "3392";
    private $db_name = "goods";
    private $username = "root";
    private $password = "nong420631";*/

    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=$this->host:$this->port;dbname=$this->db_name", "$this->username", "$this->password");
	    //$this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", "$this->username", "$this->password");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        } 

        return $this->conn;
    }
}
?>