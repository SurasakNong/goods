<?php
class Packlist{     
        // database connection and table name
        private $conn;
        private $table_name = "pack_bag_list";
    
        // object properties
        public $list_id;
        public $bag_id;
        public $list_nt;
        public $list_pcs;
        public $list_kg;
     
        // constructor
        public function __construct($db){
            $this->conn = $db;
        } 
    
    function create(){    //===== create new record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    bag_id = :bag_id,
                    list_nt = :list_nt,
                    list_pcs = :list_pcs,
                    list_kg = :list_kg";

        $stmt = $this->conn->prepare($query);
        $this->bag_id=htmlspecialchars(strip_tags($this->bag_id));        
        $this->list_nt=htmlspecialchars(strip_tags($this->list_nt));
        $this->list_pcs=htmlspecialchars(strip_tags($this->list_pcs));
        $this->list_kg=htmlspecialchars(strip_tags($this->list_kg));
        
        $stmt->bindParam(':bag_id', $this->bag_id);
        $stmt->bindParam(':list_nt', $this->list_nt);
        $stmt->bindParam(':list_pcs', $this->list_pcs);
        $stmt->bindParam(':list_kg', $this->list_kg);  

        if($stmt->execute()){
            return true;
        }    
        return false;
    } 

    // delete a record
    public function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE list_id = :list_id";
        $stmt = $this->conn->prepare($query);    
        $this->list_id = htmlspecialchars(strip_tags($this->list_id));
        $stmt->bindParam(':list_id', $this->list_id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }       

    // delete a record as bag id
    public function delete_bagid(){
        $query = "DELETE FROM " . $this->table_name . " WHERE bag_id = :bag_id";
        $stmt = $this->conn->prepare($query);    
        $this->bag_id = htmlspecialchars(strip_tags($this->bag_id));
        $stmt->bindParam(':bag_id', $this->bag_id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }  
    
    public function update(){ // update data record
        $query = "UPDATE " . $this->table_name . "
                SET           
                    bag_id = :bag_id,
                    list_nt = :list_nt,
                    list_pcs = :list_pcs,
                    list_kg = :list_kg
                WHERE list_id = :list_id";
    
        $stmt = $this->conn->prepare($query);
        $this->bag_id=htmlspecialchars(strip_tags($this->bag_id));        
        $this->list_nt=htmlspecialchars(strip_tags($this->list_nt));
        $this->list_pcs=htmlspecialchars(strip_tags($this->list_pcs));
        $this->list_kg=htmlspecialchars(strip_tags($this->list_kg));
        
        $stmt->bindParam(':bag_id', $this->bag_id);
        $stmt->bindParam(':list_nt', $this->list_nt);
        $stmt->bindParam(':list_pcs', $this->list_pcs);
        $stmt->bindParam(':list_kg', $this->list_kg);  
               
        $stmt->bindParam(':list_id', $this->list_id);    
        if($stmt->execute()){
            return true;
        }    
        return false;
    }

 
}

?>

