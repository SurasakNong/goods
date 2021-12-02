<?php
include_once 'config/core.php';
//===== required headers
header("Access-Control-Allow-Origin: ${home}");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
//===== files needed to connect to database
include_once 'config/database.php';
// get database connection
$database = new Database();
$db = $database->getConnection();
$sql = "";
if (isset($_POST['fn'])){
    $id = $_POST['id'];
    if ($_POST['fn'] == 'depart') {
        $sql = "SELECT id_depart, depart FROM depart ORDER BY depart";   
    /*}elseif($_POST['fn'] == 'group'){
        $sql = "SELECT group_id,group_name,depart FROM group_mc INNER JOIN depart on bd_id = id_depart ORDER BY group_name ASC"; 
    }elseif($_POST['fn'] == 'shift'){
        $sql = "SELECT shift_id,shift_name FROM shift ORDER BY shift_name ASC"; */
    }else{
        $sql = "";
    }
    
    
    if($sql != ""){
        // prepare the query
        $stmt = $db->prepare( $sql ); 
        $stmt->execute();
        $resultArray = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($resultArray,$row);
        }
        $database = null;
        echo json_encode($resultArray);
    }
    
}

?>



