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
            $dpsel = isset($_POST['dpsel'])?$_POST['dpsel']:"";           
            $search = htmlspecialchars(strip_tags($search));
            $dpsel = ($dpsel=="0")?"":"AND (packer_id = '${dpsel}')";
            $resultArray = array();

            // คำค้นหา
            $concat = " (
                CONCAT(pack_bill,' ',bag_no,' ',bag_name,' ',code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%";
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

        $sql = "SELECT ('data') AS key_p,bag_id, bag_no, pack_date, packer_id, pack_bill,list_nt as bag_name, bag_desc, COUNT(list_id) AS bag_amount, SUM(list_pcs) AS bag_pcs, bag_kg, SUM(list_kg) AS bag_std_kg FROM (
            SELECT pack_bag.bag_id, bag_no , pack_date, packer_id, pack_bill, pack_bag_list.list_id, pack_bag_list.list_nt,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) as bag_desc, pack_bag_list.list_pcs, pack_bag_list.list_kg, pack_bag.bag_kg FROM ((pack_bag LEFT JOIN pack_bag_list ON pack_bag.bag_id = pack_bag_list.bag_id) LEFT JOIN product ON pack_bag_list.list_nt = code) WHERE ((pack_date BETWEEN '${datefm}' AND '${dateto}' ${dpsel}) $concat_set )) as tt GROUP BY bag_id ORDER BY pack_bill,bag_no ASC";
        
        $stmt = $db->prepare($sql);  
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { //===== รายเครื่อง            
            array_push($resultArray,$row);
        } 

        $sql_all = "SELECT ('all') AS key_p,COUNT(bag_id) as bag_n,SUM(bag_amount) as bag_amount,SUM(bag_pcs) as bag_pcs, SUM(bag_kg) as bag_kg, SUM(bag_std_kg) as bag_std_kg FROM(
            SELECT bag_id, bag_no, pack_date, packer_id, pack_bill,list_nt as bag_name, bag_desc, COUNT(list_id) AS bag_amount, SUM(list_pcs) AS bag_pcs, bag_kg, SUM(list_kg) AS bag_std_kg FROM (
                        SELECT pack_bag.bag_id, bag_no , pack_date, packer_id, pack_bill, pack_bag_list.list_id, pack_bag_list.list_nt,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) as bag_desc, pack_bag_list.list_pcs, pack_bag_list.list_kg, pack_bag.bag_kg FROM ((pack_bag LEFT JOIN pack_bag_list ON pack_bag.bag_id = pack_bag_list.bag_id) LEFT JOIN product ON pack_bag_list.list_nt = code) WHERE ((pack_date BETWEEN '${datefm}' AND '${dateto}' ${dpsel}) $concat_set )) AS tt GROUP BY bag_id) AS tt2";

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



