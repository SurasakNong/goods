<?php
class Packbag{     
        // database connection and table name
        private $conn;
        private $table_name = "pack_bag";
    
        // object properties
        public $bag_id;
        public $pack_date;
        public $packer_id;
        public $pack_bill;
        public $bag_no;
        public $bag_name;
        public $bag_amount;
        public $bag_pcs;   
        public $bag_kg; 
        public $bag_std_kg;   
     
        // constructor
        public function __construct($db){
            $this->conn = $db;
        } 
    
    function create(){    //===== create new record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    pack_date = :pack_date,
                    packer_id = :packer_id,
                    pack_bill = :pack_bill,
                    bag_no = :bag_no,
                    bag_name = :bag_name,
                    bag_amount = :bag_amount,
                    bag_pcs = :bag_pcs,
                    bag_kg = :bag_kg,
                    bag_std_kg = :bag_std_kg";

        $stmt = $this->conn->prepare($query);
        $this->pack_date=htmlspecialchars(strip_tags($this->pack_date));        
        $this->packer_id=htmlspecialchars(strip_tags($this->packer_id));
        $this->pack_bill=htmlspecialchars(strip_tags($this->pack_bill));
        $this->bag_no=htmlspecialchars(strip_tags($this->bag_no));
        $this->bag_name=htmlspecialchars(strip_tags($this->bag_name));
        $this->bag_amount=htmlspecialchars(strip_tags($this->bag_amount));   
        $this->bag_pcs=htmlspecialchars(strip_tags($this->bag_pcs));     
        $this->bag_kg=htmlspecialchars(strip_tags($this->bag_kg));   
        $this->bag_std_kg=htmlspecialchars(strip_tags($this->bag_std_kg));   

        $stmt->bindParam(':pack_date', $this->pack_date);
        $stmt->bindParam(':packer_id', $this->packer_id);
        $stmt->bindParam(':pack_bill', $this->pack_bill);
        $stmt->bindParam(':bag_no', $this->bag_no);
        $stmt->bindParam(':bag_name', $this->bag_name);
        $stmt->bindParam(':bag_amount', $this->bag_amount);
        $stmt->bindParam(':bag_pcs', $this->bag_pcs); 
        $stmt->bindParam(':bag_kg', $this->bag_kg); 
        $stmt->bindParam(':bag_std_kg', $this->bag_std_kg);      

        if($stmt->execute()){
            return true;
        }    
        return false;
    } 

    // delete a record
    public function delete(){
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
                    bag_no = :bag_no,
                    bag_name = :bag_name,
                    bag_amount = :bag_amount,
                    bag_pcs = :bag_pcs,
                    bag_kg = :bag_kg,
                    bag_std_kg = :bag_std_kg
                WHERE bag_id = :bag_id";
    
        $stmt = $this->conn->prepare($query);
        $this->bag_no=htmlspecialchars(strip_tags($this->bag_no));
        $this->bag_name=htmlspecialchars(strip_tags($this->bag_name));
        $this->bag_amount=htmlspecialchars(strip_tags($this->bag_amount));   
        $this->bag_pcs=htmlspecialchars(strip_tags($this->bag_pcs));     
        $this->bag_kg=htmlspecialchars(strip_tags($this->bag_kg));   
        $this->bag_std_kg=htmlspecialchars(strip_tags($this->bag_std_kg));   

        $stmt->bindParam(':bag_no', $this->bag_no);
        $stmt->bindParam(':bag_name', $this->bag_name);
        $stmt->bindParam(':bag_amount', $this->bag_amount);
        $stmt->bindParam(':bag_pcs', $this->bag_pcs); 
        $stmt->bindParam(':bag_kg', $this->bag_kg); 
        $stmt->bindParam(':bag_std_kg', $this->bag_std_kg);
               
        $stmt->bindParam(':bag_id', $this->bag_id);    
        if($stmt->execute()){
            return true;
        }    
        return false;
    }

    public function updateKg(){ // update data record
        $query = "UPDATE " . $this->table_name . "
                SET  bag_kg = :bag_kg
                WHERE bag_id = :bag_id";    
        $stmt = $this->conn->prepare($query);   
        $this->bag_kg=htmlspecialchars(strip_tags($this->bag_kg));   
        $stmt->bindParam(':bag_kg', $this->bag_kg);                
        $stmt->bindParam(':bag_id', $this->bag_id);    
        if($stmt->execute()){
            return true;
        }    
        return false;
    }

    function bagno_Exit(){ //===== ตรวจสอบว่า bag_no นี้ซืำหรือไม่
        $sql="SELECT *
        FROM " . $this->table_name. "
        WHERE (bag_no = :bag_no AND pack_date = :pack_date AND pack_bill = :pack_bill AND packer_id = :packer_id)";

        $stmt = $this->conn->prepare($sql);
        $this->bag_no = htmlspecialchars(strip_tags($this->bag_no));
        $this->pack_date=htmlspecialchars(strip_tags($this->pack_date));  
        $this->pack_bill=htmlspecialchars(strip_tags($this->pack_bill));
        $this->packer_id = htmlspecialchars(strip_tags($this->packer_id));

        $stmt->bindParam(':bag_no',$this->bag_no);
        $stmt->bindParam(':pack_date', $this->pack_date);
        $stmt->bindParam(':pack_bill', $this->pack_bill);
        $stmt->bindParam(':packer_id',$this->packer_id);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num>0){
            return true;
        }
        return false;
    }

    function bag_id(){ //===== หา bag_id
        $sql="SELECT *
        FROM " . $this->table_name. "
        WHERE (bag_no = :bag_no AND pack_date = :pack_date AND pack_bill = :pack_bill AND packer_id = :packer_id) LIMIT 0,1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':bag_no',$this->bag_no);
        $stmt->bindParam(':pack_date', $this->pack_date);
        $stmt->bindParam(':pack_bill', $this->pack_bill);
        $stmt->bindParam(':packer_id',$this->packer_id);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num>0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->bag_id = $row['bag_id'];            
            return true;
        }
        return false;
    }

    

 
}

?>

