<?php
include_once 'config/core.php';
//===== required headers
header("Access-Control-Allow-Origin: ${home}");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
// database connection will be here
// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/monitor.php';

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

$myclass = new Monitor($db);
$data = json_decode(file_get_contents("php://input"));
if(!empty($data->jwt)){ 
    try { 
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));   // decode jwt
        if(!empty($data->acc) && $data->acc === "setzero"){ //ทำการกำหนดค่าให้เป็น ศูนย์
            $myclass->mc = $data->mc;            
            if( !empty($myclass->mc) && $myclass->set_zero()){
                http_response_code(200);
                echo json_encode(array("message" => "Mc was set to zero."));
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to set zero."));
            } 
        }else if(!empty($data->acc) && $data->acc === "setzero_all"){ //ปรับปรุงแก้ไขข้อมูล
            if($myclass->set_zero_all()){
                http_response_code(200);
                echo json_encode(array("message" => "All mc was set to zero."));
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to set zero all."));
            } 
        }else if(!empty($data->acc) && $data->acc === "set_shift"){ //ปรับปรุงกะให้เป็นปัจจุบัน
            $myclass->mc = $data->mc; 
            if($myclass->shift_set()){
                $myclass->shift_update();
                http_response_code(200);
                echo json_encode(array("message" => "Set shift to now."));
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to set shift."));
            } 
        }else if(!empty($data->acc) && $data->acc === "set_shift_all"){ //ปรับปรุงกะทุกเครื่องให้เป็นปัจจุบัน
            if($myclass->shift_set_all()){
                http_response_code(200);
                echo json_encode(array("message" => "All set shift to now."));
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to set shift all."));
            } 
        }else if(!empty($data->acc) && $data->acc === "update_data"){ //ปรับปรุงข้อมูล
            $myclass->mc = $data->mc_data; 
            $myclass->head_str = $data->head_data; 
            $rest = substr($data->head_data, -1);            
            $myclass->data_str = ($rest == 't')?$data->ed_data*600:$data->ed_data; 
            if($myclass->data_update()){
                http_response_code(200);
                echo json_encode(array("message" => "Set data success."));
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to set data."));
            }

        }else{
            http_response_code(400); 
            echo json_encode(array("message" => "Unable to access data."));
        }
    }
    
    catch (Exception $e){    // if decode fails, it means jwt is invalid
        http_response_code(400);    
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}else{  // show error message if jwt is empty
    http_response_code(401); 
    echo json_encode(array("message" => "Access denied."));
}


?>

