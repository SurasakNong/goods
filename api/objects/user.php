<?php
class User{     
        // database connection and table name
        private $conn;
        private $table_name = "users";
    
        // object properties
        public $id;
        public $firstname;
        public $lastname;
        public $depart;
        public $username;
        public $password;
        public $newpassword;
        public $type;
        public $dp_name;
    
        // constructor
        public function __construct($db){
            $this->conn = $db;
        } 
    
    function create(){    //===== create new user record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    firstname = :firstname,
                    lastname = :lastname,
                    id_depart = :depart,
                    username = :username,
                    password = :password,
                    type = :type";

        $stmt = $this->conn->prepare($query);
        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->depart=htmlspecialchars(strip_tags($this->depart));
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->type=htmlspecialchars(strip_tags($this->type));

        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':depart', $this->depart);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':type', $this->type);
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);

        if($stmt->execute()){
            return true;
        }    
        return false;
    } 

    // delete a record
    public function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);    
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
       
    public function usernameExists(){    //===== check if given username exist in the database
        $query = "SELECT id, firstname, lastname, users.id_depart, depart.depart, type, username, password 
        FROM " . $this->table_name . " LEFT JOIN depart ON users.id_depart = depart.id_depart
        WHERE username = ? LIMIT 0,1";    
        $stmt = $this->conn->prepare( $query );   
        $this->username=htmlspecialchars(strip_tags($this->username));   
        $stmt->bindParam(1, $this->username);    
        $stmt->execute();    
        $num = $stmt->rowCount();     
        if($num>0){    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);    
            $this->id = $row['id'];
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
            $this->depart = $row['id_depart'];
            $this->username = $row['username'];
            $this->password = $row['password'];     
            $this->type = $row['type'];    
            $this->dp_name = $row['depart']; 
            return true;
        }    
        return false;
    }


    public function newUsernameExit(){ //===== ตรวจสอบว่า username นี้ซืำกับของผู้อื่นหรือไม่ (นอกจากจะเป็นของผู้ใช้เอง)
        $sql="SELECT id, firstname, lastname, username
        FROM " . $this->table_name. "
        WHERE (username = :username AND id != :id)";

        $stmt = $this->conn->prepare($sql);
        $this->username = htmlspecialchars(strip_tags($this->username));

        $stmt->bindParam(':username',$this->username);
        $stmt->bindParam(':id',$this->id);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num>0){  
            return true;
        }
        return false;
    }
    
    public function update(){ // update a user record
        $password_set = "";    
        if(!empty($this->newpassword)){
            $password_set = ", password = :password";
        }
        $query = "UPDATE " . $this->table_name . "
                SET
                    firstname = :firstname,
                    lastname = :lastname,
                    id_depart = :depart,
                    type = :type,
                    username = :username
                    {$password_set}
                WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);    
        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->depart=htmlspecialchars(strip_tags($this->depart));
        $this->type=htmlspecialchars(strip_tags($this->type));   
        $this->username=htmlspecialchars(strip_tags($this->username));   

        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':depart', $this->depart);    
        $stmt->bindParam(':type', $this->type);  
        $stmt->bindParam(':username', $this->username);  

        if(!empty($this->newpassword)){
            $this->newpassword=htmlspecialchars(strip_tags($this->newpassword));
            $password_hash = password_hash($this->newpassword, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }    
        $stmt->bindParam(':id', $this->id);    
        if($stmt->execute()){
            return true;
        }    
        return false;
    }

 
}

?>

