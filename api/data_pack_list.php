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

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
// get database connection
$database = new Database();
$db = $database->getConnection();
$sql = "";
$data = json_decode(file_get_contents("php://input"));
$jwt = $data->jwt;
if(!empty($jwt)){
    try{
        $decoded = JWT::decode($jwt, $key, array('HS256'));   // ถอดรหัส jwt  
            $bag_id = $data->bag_id;

            $perpage = $data->perpage;
            $page = $data->page;
            $rowStart = ($page-1)*$perpage;

            // ข้อมูลที่ต้องการให้แสดง
            $sql = "SELECT pack_bag_list.*,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) AS list_desc FROM pack_bag_list INNER JOIN product ON list_nt = CODE WHERE bag_id = $bag_id ORDER BY list_id DESC LIMIT $rowStart , $perpage";
                $stmt = $db->prepare( $sql );  
                $stmt->execute();
                $num = $stmt->rowCount();   
                $resultArray = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    array_push($resultArray,$row);
                }        // จำนวนข้อมูลทั้งหมด
                    $sql2 = "SELECT pack_bag_list.*,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) AS list_desc FROM pack_bag_list INNER JOIN product ON list_nt = CODE WHERE bag_id = $bag_id";
                    $stmt2 = $db->prepare( $sql2 );   
                    $stmt2->execute();
                   $numall = $stmt2->rowCount();  
                   $pcs_all = 0;
                   $kg_all = 0;
                   while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $pcs_all += $row2["list_pcs"];
                    $kg_all += $row2["list_kg"];
                    }
                $allpage = ceil($numall/$perpage);
                $database = null; 
                echo json_encode(
                    array(
                        "data" => $resultArray,
                        "page_all" => $allpage,
                        "pcs_all" => $pcs_all,
                        "kg_all" => $kg_all,
                        "list_all" => $numall
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



