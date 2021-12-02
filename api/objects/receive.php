<?php
class Receive{     
        // database connection and table name
        private $conn;
        private $table_name = "receive";
    
        // object properties
        public $id;
        public $date_rec;
        public $bill_rec;
        public $dp_rec;
        public $dp_post;
        public $code;
        public $pcs;
        public $wt;     
     
        // constructor
        public function __construct($db){
            $this->conn = $db;
        } 
    
    function create(){    //===== create new record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    date_rec = :date_rec,
                    bill_rec = :bill,
                    dp_rec = :dp_rec,
                    dp_post = :dp_post,
                    code = :code,
                    pcs = :pcs,
                    wt = :wt";

        $stmt = $this->conn->prepare($query);
        $this->date_rec=htmlspecialchars(strip_tags($this->date_rec));        
        $this->bill=htmlspecialchars(strip_tags($this->bill));
        $this->dp_rec=htmlspecialchars(strip_tags($this->dp_rec));
        $this->dp_post=htmlspecialchars(strip_tags($this->dp_post));
        $this->code=htmlspecialchars(strip_tags($this->code));
        $this->pcs=htmlspecialchars(strip_tags($this->pcs));   
        $this->wt=htmlspecialchars(strip_tags($this->wt));     

        $stmt->bindParam(':date_rec', $this->date_rec);
        $stmt->bindParam(':bill', $this->bill);
        $stmt->bindParam(':dp_rec', $this->dp_rec);
        $stmt->bindParam(':dp_post', $this->dp_post);
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':pcs', $this->pcs);
        $stmt->bindParam(':wt', $this->wt);      

        if($stmt->execute()){
            return true;
        }    
        return false;
    } 

    // delete a record
    public function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id_rec = :id";
        $stmt = $this->conn->prepare($query);    
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }       
    
    public function update(){ // update data record
        $query = "UPDATE " . $this->table_name . "
                SET                 
                pcs = :pcs
                WHERE id_rec = :id";
    
        $stmt = $this->conn->prepare($query);
        $this->pcs=htmlspecialchars(strip_tags($this->pcs));

        $stmt->bindParam(':pcs', $this->pcs);
               
        $stmt->bindParam(':id', $this->id);    
        if($stmt->execute()){
            return true;
        }    
        return false;
    }

 
}

?>

