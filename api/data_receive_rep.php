<?php
include_once 'config/core.php';
//===== required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
//===== files needed to connect to database
include_once 'config/database.php';

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
// get database connection
$database = new Database(); 
$db = $database->getConnection();
$sql = "";
$jwt = isset($_POST['jwt'])?$_POST['jwt']:"";
if(!empty($jwt)){
    try{
        $decoded = JWT::decode($jwt, $key, array('HS256'));   // ถอดรหัส jwt    
            $datefm = isset($_POST['datefm'])?$_POST['datefm']:"";
            $dateto = isset($_POST['dateto'])?$_POST['dateto']:"";
            $search = isset($_POST['search'])?$_POST['search']:"";
            $depart_rec = isset($_POST['dp_rec'])?$_POST['dp_rec']:"";     
            $depart_post = isset($_POST['dp_post'])?$_POST['dp_post']:"";        
            $search = htmlspecialchars(strip_tags($search));
            $depart_rec = ($depart_rec=="0")?"":" AND (dp_rec = '${depart_rec}') ";
            $depart_post = ($depart_post=="0")?"":" AND (dp_post = '${depart_post}') ";
            $resultArray = array();


$sql_gr2 = "SELECT ('gr2') AS key_p, search, SUM(nn) as nn, SUM(pcs) as pcs FROM(
    SELECT search,code,spec,SUM(nn) as nn, SUM(pcs) AS pcs FROM(
    SELECT date_rec,bill_rec,receive.code,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) AS spec, search,1 AS nn, receive.pcs FROM receive INNER JOIN product ON receive.code = product.code  WHERE ((date_rec BETWEEN '${datefm}' AND '${dateto}') ${depart_rec} ${depart_post} AND CONCAT('#',bill_rec,' ',receive.code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%${search}%') 
        ) AS T_gr1 GROUP BY code ORDER BY code ASC
        ) AS T_gr2 GROUP BY search ORDER BY search ASC";

$stmt_gr2 = $db->prepare( $sql_gr2 );  
$stmt_gr2->execute();

while($row2 = $stmt_gr2->fetch(PDO::FETCH_ASSOC)) { //===== กลุ่ม 2 
    $ss_gr2 = $row2['search'];
    $sql_gr1 = "SELECT ('gr1') AS key_p, search, code, spec, SUM(nn) as nn, SUM(pcs) AS pcs FROM(
        SELECT date_rec,bill_rec,receive.code,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) AS spec, search,1 AS nn, receive.pcs FROM receive INNER JOIN product ON receive.code = product.code  WHERE ((date_rec BETWEEN '${datefm}' AND '${dateto}') ${depart_rec} ${depart_post} AND CONCAT('#',bill_rec,' ',receive.code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%${search}%' AND search = '${ss_gr2}') 
            ) AS T_gr1 GROUP BY code ORDER BY code ASC";
    
    $stmt_gr1 = $db->prepare( $sql_gr1 );  
    $stmt_gr1->execute();
    while($row1 = $stmt_gr1->fetch(PDO::FETCH_ASSOC)) { //===== กลุ่ม 1  
        $ss_gr1 = $row1['code'];
        $sql = "SELECT ('data') AS key_p,date_rec,bill_rec,receive.code,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) AS spec, search, receive.pcs FROM receive INNER JOIN product ON receive.code = product.code  
        WHERE ((date_rec BETWEEN '${datefm}' AND '${dateto}') ${depart_rec} ${depart_post} AND CONCAT('#',bill_rec,' ',receive.code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%${search}%' AND receive.code = '${ss_gr1}' AND search = '${ss_gr2}')";
        
        $stmt = $db->prepare($sql);  
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { //===== รายการข้อมูล            
            array_push($resultArray,$row);
        } 
        array_push($resultArray,$row1);
    } 
    array_push($resultArray,$row2); 
} 


$sql_all = "SELECT ('all') AS key_p,sum(nn) as nn, sum(pcs) AS pcs FROM(
    SELECT search, SUM(nn) as nn, SUM(pcs) as pcs FROM(
    SELECT search,code,spec,SUM(nn) as nn, SUM(pcs) AS pcs FROM(
    SELECT date_rec,bill_rec,receive.code,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) AS spec, search,1 AS nn, receive.pcs FROM receive INNER JOIN product ON receive.code = product.code  WHERE ((date_rec BETWEEN '${datefm}' AND '${dateto}') ${depart_rec} ${depart_post} AND CONCAT('#',bill_rec,' ',receive.code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%${search}%') 
        ) AS T_gr1 GROUP BY code ORDER BY code ASC
        ) AS T_gr2 GROUP BY search ORDER BY search ASC
        ) AS T_all";
$stmt_all = $db->prepare($sql_all);  
$stmt_all->execute();
while($row_all = $stmt_all->fetch(PDO::FETCH_ASSOC)) { //===== รวมทั้งหมด  
    array_push($resultArray,$row_all);
    
} 
    $database = null; 
    http_response_code(200);   
    echo json_encode(
        array(
            "data" => $resultArray
        )
    );    

    }
    catch (Exception $e){    //ถอดรหัส JWT ไม่ถูกต้อง
        http_response_code(402);    
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        )); 
    }
}else{ //ไม่พบ JWT
    http_response_code(401); 
    echo json_encode(array("message" => "Access denied."));
}   


?>



