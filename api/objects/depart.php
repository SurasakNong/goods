<?php
class Depart{     
        // database connection and table name
        private $conn;
        private $table_name = "depart";
    
        // object properties
        public $id;
        public $depart;
     
        // constructor
        public function __construct($db){
            $this->conn = $db;
        } 
    
    function create(){    //===== create new record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    depart = :depart";

        $stmt = $this->conn->prepare($query);
        $this->firstname=htmlspecialchars(strip_tags($this->depart));
        $stmt->bindParam(':depart', $this->depart);

        if($stmt->execute()){
            return true;
        }    
        return false;
    } 

    // delete a record
    public function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id_depart = :id";
        $stmt = $this->conn->prepare($query);    
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
       
    function nameExists(){    //===== check if given name exist in the database
        $query = "SELECT *
                FROM " . $this->table_name . "
                WHERE depart = ?
                LIMIT 0,1";    
        $stmt = $this->conn->prepare( $query );   
        $this->depart=htmlspecialchars(strip_tags($this->depart));   
        $stmt->bindParam(1, $this->depart);    
        $stmt->execute();    
        $num = $stmt->rowCount();    
        if($num>0){    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);    
            $this->id = $row['id_depart'];
            $this->depart = $row['depart']; 
            return true;
        }    
        return false;
    }


    function newnameExit(){ //===== ตรวจสอบว่า name นี้ซืำหรือไม่
        $sql="SELECT *
        FROM " . $this->table_name. "
        WHERE (depart = :depart AND id_depart != :id)";

        $stmt = $this->conn->prepare($sql);
        $this->depart = htmlspecialchars(strip_tags($this->depart));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':depart',$this->depart);
        $stmt->bindParam(':id',$this->id);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num>0){
            return true;
        }
        return false;
    }
    
    public function update(){ // update a user record
        $query = "UPDATE " . $this->table_name . "
                SET
                    depart = :depart
                WHERE id_depart = :id";
    
        $stmt = $this->conn->prepare($query);    
        $this->depart=htmlspecialchars(strip_tags($this->depart)); 

        $stmt->bindParam(':depart', $this->depart);
        $stmt->bindParam(':id', $this->id);    
        if($stmt->execute()){
            return true;
        }    
        return false;
    }

 
}

?>

