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
//$jwt = isset($_POST['jwt'])?$_POST['jwt']:"";
$jwt = $data->jwt;
if(!empty($jwt)){
    try{
        $decoded = JWT::decode($jwt, $key, array('HS256'));   // ถอดรหัส jwt   
            $packdate = DateYmd($data->packdate);
            $packbill = $data->packbill;
            $packerid = $data->packerid;
            $perpage = $data->perpage;
            $page = $data->page;
            $search = $data->search;
            $search = htmlspecialchars(strip_tags($search));
            $rowStart = ($page-1)*$perpage;

            // คำค้นหา
            $concat = " (
            CONCAT(bag_no,' ',bag_name,' ',code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%";
            $concat_set = "";
            if($search != ''){
                $search_ex = explode(" ",$search);
                $search_count = count($search_ex);                
                for($i=0; $i<$search_count; $i++){
                    $concat_set = $concat_set.$concat.$search_ex[$i]."%')";
                    if($i < ($search_count-1)){
                        $concat_set = $concat_set." AND ";
                    }
                }
                $concat_set = "AND (".$concat_set.")";  
            }

            // ข้อมูลที่ต้องการให้แสดง
            $sql = "SELECT bag_id, bag_no, pack_date, packer_id, pack_bill,list_nt as bag_name, bag_desc, COUNT(list_id) AS bag_amount, SUM(list_pcs) AS bag_pcs, bag_kg, SUM(list_kg) AS bag_std_kg FROM (
                SELECT pack_bag.bag_id, bag_no , pack_date, packer_id, pack_bill, pack_bag_list.list_id, pack_bag_list.list_nt,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) as bag_desc, pack_bag_list.list_pcs, pack_bag_list.list_kg, pack_bag.bag_kg FROM ((pack_bag LEFT JOIN pack_bag_list ON pack_bag.bag_id = pack_bag_list.bag_id) LEFT JOIN product ON pack_bag_list.list_nt = code) WHERE ((pack_date = '$packdate' AND packer_id = $packerid AND pack_bill = '$packbill') $concat_set )) as tt GROUP BY bag_id ORDER BY bag_id DESC LIMIT $rowStart , $perpage";

                $stmt = $db->prepare( $sql );  
                $stmt->execute();
                $num = $stmt->rowCount();   
                $resultArray = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    array_push($resultArray,$row);
                }        // จำนวนข้อมูลทั้งหมด
                    $sql2 = "SELECT bag_id, bag_no, pack_date, packer_id, pack_bill,list_nt as bag_name, bag_desc, COUNT(list_id) AS bag_amount, SUM(list_pcs) AS bag_pcs, bag_kg, SUM(list_kg) AS bag_std_kg FROM (
                        SELECT pack_bag.bag_id, bag_no , pack_date, packer_id, pack_bill, pack_bag_list.list_id, pack_bag_list.list_nt,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) as bag_desc, pack_bag_list.list_pcs, pack_bag_list.list_kg, pack_bag.bag_kg FROM ((pack_bag LEFT JOIN pack_bag_list ON pack_bag.bag_id = pack_bag_list.bag_id) LEFT JOIN product ON pack_bag_list.list_nt = code) WHERE ((pack_date = '$packdate' AND packer_id = $packerid AND pack_bill = '$packbill') $concat_set )) as tt GROUP BY bag_id";
                        
                    $stmt2 = $db->prepare( $sql2 );   
                    $stmt2->execute();
                   $numall = $stmt2->rowCount();  
                   $pcs_all = 0;
                   $kg_all = 0;
                   while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $pcs_all += $row2["bag_pcs"];
                    $kg_all += $row2["bag_kg"];
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

function DateYmd($date)
    {
        $get_date = explode("/", $date);
        return $get_date['2'] . "-" . $get_date['1'] . "-" . $get_date['0'];
    }

?>



