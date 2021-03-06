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
$jwt = isset($_POST['jwt'])?$_POST['jwt']:"";
if(!empty($jwt)){
    try{
        $decoded = JWT::decode($jwt, $key, array('HS256'));   // ถอดรหัส jwt        
            $perpage = isset($_POST['perpage'])?(int)$_POST['perpage']:10;
            $page = isset($_POST['page'])?(int)$_POST['page']:1;
            $daterec = isset($_POST['daterec'])?DateYmd($_POST['daterec']):"";
            $billrec = isset($_POST['bill'])?$_POST['bill']:"";
            $dprec = isset($_POST['dp_rec'])?$_POST['dp_rec']:"";
            $dppost = isset($_POST['dp_post'])?$_POST['dp_post']:"";
            $search = isset($_POST['search'])?$_POST['search']:"";
            $search = htmlspecialchars(strip_tags($search));
            $rowStart = ($page-1)*$perpage;

            // คำค้นหา
            $concat = " (
                CONCAT(receive.code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%";
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
            $sql = "SELECT receive.*,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) AS spec, product.search, product.pcs AS pcs_std,product.wt AS wt_std FROM receive INNER JOIN product ON receive.code = product.code  WHERE ((date_rec = '$daterec') AND (bill_rec = '$billrec' AND dp_rec = $dprec AND dp_post = $dppost) AND CONCAT(receive.code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%".$search."%') ORDER BY id_rec DESC LIMIT $rowStart , $perpage";
                $stmt = $db->prepare( $sql );  
                $stmt->execute();
                $num = $stmt->rowCount();   
                $resultArray = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    array_push($resultArray,$row);
                }        // จำนวนข้อมูลทั้งหมด ที่แสดงในตาราง

                    $pcs_all = 0;       
                    $kg_all = 0;             
                    $sql2 = "SELECT receive.*,CONCAT(dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label) AS spec, product.search, product.pcs AS pcs_std,product.wt AS wt_std FROM receive INNER JOIN product ON receive.code = product.code  WHERE ((date_rec = '$daterec') AND (bill_rec = '$billrec' AND dp_rec = $dprec AND dp_post = $dppost) AND CONCAT(receive.code,' ',dia,' ',color,' ',knot,' ',ms,ms_unit,'x',md,md_unit,'x',ml,ml_unit,' ',label,' ',search) LIKE '%".$search."%')";
                    $stmt2 = $db->prepare( $sql2 );   
                    $stmt2->execute();
                    $numall = $stmt2->rowCount();  
                    while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        $pcs_all += $row2["pcs"];
                        $kg_all += ($row2["pcs"]*$row2["wt"]);
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



