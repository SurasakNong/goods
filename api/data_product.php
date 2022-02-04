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
if(!empty($data->jwt)){
    try{
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));   // ถอดรหัส jwt   
            $perpage = (int)$data->perpage > 0? (int)$data->perpage:10;
            $page = (int)$data->perpage > 0? (int)$data->perpage:1;
            $search = $data->search;
            $search = htmlspecialchars(strip_tags($search));
            $rowStart = ($page-1)*$perpage;
            // ข้อมูลที่ต้องการให้แสดง
            $sql = "SELECT * FROM product WHERE ( CONCAT(code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%".$search."%') ORDER BY code ASC LIMIT $rowStart , $perpage";
                $stmt = $db->prepare( $sql );  
                $stmt->execute();
                $num = $stmt->rowCount();   
                $resultArray = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    array_push($resultArray,$row);
                }        // จำนวนข้อมูลทั้งหมด
                    $sql2 = "SELECT * FROM product WHERE CONCAT(code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%".$search."%'";
                    $stmt2 = $db->prepare( $sql2 );   
                    $stmt2->execute();
                   $numall = $stmt2->rowCount();  
                $allpage = ceil($numall/$perpage);
                $database = null; 
                echo json_encode(
                    array(
                        "data" => $resultArray,
                        "page_all" => $allpage
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



