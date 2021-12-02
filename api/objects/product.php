<?php
class Product{     
        // database connection and table name
        private $conn;
        private $table_name = "product";
    
        // object properties
        public $id;
        public $code;
        public $dia;
        public $color;
        public $knot;
        public $ms;
        public $ms_unit;
        public $md;
        public $md_unit;
        public $ml;
        public $ml_unit;
        public $label;
        public $pcs;
        public $wt;
        public $search;       
     
        // constructor
        public function __construct($db){
            $this->conn = $db;
        } 
    
    function create(){    //===== create new record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    code = :code,
                    dia = :dia,
                    color = :color,
                    knot = :knot,
                    ms = :ms,
                    ms_unit = :ms_unit,
                    md = :md,
                    md_unit = :md_unit,
                    ml = :ml,
                    ml_unit = :ml_unit,
                    label = :label,
                    pcs = :pcs,
                    wt = :wt,
                    search = :search";

        $stmt = $this->conn->prepare($query);
        $this->code=htmlspecialchars(strip_tags($this->code));        
        $this->dia=htmlspecialchars(strip_tags($this->dia));
        $this->color=htmlspecialchars(strip_tags($this->color));
        $this->knot=htmlspecialchars(strip_tags($this->knot));
        $this->ms=htmlspecialchars(strip_tags($this->ms));
        $this->ms_unit=htmlspecialchars(strip_tags($this->ms_unit));
        $this->md=htmlspecialchars(strip_tags($this->md));
        $this->md_unit=htmlspecialchars(strip_tags($this->md_unit));
        $this->ml=htmlspecialchars(strip_tags($this->ml));
        $this->ml_unit=htmlspecialchars(strip_tags($this->ml_unit));
        $this->label=htmlspecialchars(strip_tags($this->label));
        $this->pcs=htmlspecialchars(strip_tags($this->pcs));
        $this->wt=htmlspecialchars(strip_tags($this->wt));
        $this->search=htmlspecialchars(strip_tags($this->search));        

        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':dia', $this->dia);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':knot', $this->knot);
        $stmt->bindParam(':ms', $this->ms);
        $stmt->bindParam(':ms_unit', $this->ms_unit);
        $stmt->bindParam(':md', $this->md);
        $stmt->bindParam(':md_unit', $this->md_unit);
        $stmt->bindParam(':ml', $this->ml);
        $stmt->bindParam(':ml_unit', $this->ml_unit);
        $stmt->bindParam(':label', $this->label);
        $stmt->bindParam(':pcs', $this->pcs);
        $stmt->bindParam(':wt', $this->wt);
        $stmt->bindParam(':search', $this->search);        

        if($stmt->execute()){
            return true;
        }    
        return false;
    } 

    // delete a record
    public function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id_prod = :id";
        $stmt = $this->conn->prepare($query);    
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
       
    function codeExists(){    //===== check if given code exist in the database
        $query = "SELECT *
                FROM " . $this->table_name . "
                WHERE code = ?
                LIMIT 0,1";    
        $stmt = $this->conn->prepare( $query );   
        $this->code=htmlspecialchars(strip_tags($this->code));   
        $stmt->bindParam(1, $this->code);    
        $stmt->execute();    
        $num = $stmt->rowCount();    
        if($num>0){    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);    
            $this->id = $row['id_prod'];
            $this->code = $row['code']; 
            $this->dia = $row['dia'];
            $this->color = $row['color']; 
            $this->knot = $row['knot']; 
            $this->ms = $row['ms'];
            $this->ms_unit = $row['ms_unit']; 
            $this->md = $row['md'];
            $this->md_unit = $row['md_unit']; 
            $this->ml = $row['ml'];
            $this->ml_unit = $row['ml_unit']; 
            $this->label = $row['label'];
            $this->pcs = $row['pcs']; 
            $this->wt = $row['wt']; 
            $this->search = $row['search'];
            
            return true;
        }    
        return false;
    }


    function newcodeExit(){ //===== ตรวจสอบว่า code นี้ซืำหรือไม่
        $sql="SELECT *
        FROM " . $this->table_name. "
        WHERE (code = :code AND id_prod != :id)";

        $stmt = $this->conn->prepare($sql);
        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':code',$this->code);
        $stmt->bindParam(':id',$this->id);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num>0){
            return true;
        }
        return false;
    }
    
    public function update(){ // update data record
        $query = "UPDATE " . $this->table_name . "
                SET
                    code = :code,
                    dia = :dia,
                    color = :color,
                    knot = :knot,
                    ms = :ms,
                    ms_unit = :ms_unit,
                    md = :md,
                    md_unit = :md_unit,
                    ml = :ml,
                    ml_unit = :ml_unit,
                    label = :label,
                    pcs = :pcs,
                    wt = :wt,
                    search = :search
                WHERE id_prod = :id";
    
        $stmt = $this->conn->prepare($query);    
        $this->code=htmlspecialchars(strip_tags($this->code));        
        $this->dia=htmlspecialchars(strip_tags($this->dia));
        $this->color=htmlspecialchars(strip_tags($this->color));
        $this->knot=htmlspecialchars(strip_tags($this->knot));
        $this->ms=htmlspecialchars(strip_tags($this->ms));
        $this->ms_unit=htmlspecialchars(strip_tags($this->ms_unit));
        $this->md=htmlspecialchars(strip_tags($this->md));
        $this->md_unit=htmlspecialchars(strip_tags($this->md_unit));
        $this->ml=htmlspecialchars(strip_tags($this->ml));
        $this->ml_unit=htmlspecialchars(strip_tags($this->ml_unit));
        $this->label=htmlspecialchars(strip_tags($this->label));
        $this->pcs=htmlspecialchars(strip_tags($this->pcs));
        $this->wt=htmlspecialchars(strip_tags($this->wt));
        $this->search=htmlspecialchars(strip_tags($this->search));

        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':dia', $this->dia);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':knot', $this->knot);
        $stmt->bindParam(':ms', $this->ms);
        $stmt->bindParam(':ms_unit', $this->ms_unit);
        $stmt->bindParam(':md', $this->md);
        $stmt->bindParam(':md_unit', $this->md_unit);
        $stmt->bindParam(':ml', $this->ml);
        $stmt->bindParam(':ml_unit', $this->ml_unit);
        $stmt->bindParam(':label', $this->label);
        $stmt->bindParam(':pcs', $this->pcs);
        $stmt->bindParam(':wt', $this->wt);
        $stmt->bindParam(':search', $this->search);        
        $stmt->bindParam(':id', $this->id);    
        if($stmt->execute()){
            return true;
        }    
        return false;
    }

 
}

?>

