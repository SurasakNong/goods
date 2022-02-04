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
include_once 'objects/pack_bag.php';
include_once 'objects/pack_list.php';

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

$myclass = new Packbag($db);
$delclass = new Packlist($db);
$data = json_decode(file_get_contents("php://input"));
$acc = $data->acc;
$jwt = $data->jwt;
if($jwt){ 
    // if decode succeed, show user details
    try { 
        $decoded = JWT::decode($jwt, $key, array('HS256'));   // decode jwt
        if($acc == "add"){ //ทำการเพิ่มข้อมูล
            $myclass->bag_no = strtoupper($data->bag_no);
            $myclass->pack_date = DateYmd($data->pack_date);
            $myclass->pack_bill = $data->pack_bill;
            $myclass->packer_id = $data->packer_id;
            $myclass->bag_name = '';
            $myclass->bag_amount = '0';
            $myclass->bag_pcs = '0';
            $myclass->bag_kg = '0';
            $myclass->bag_std_kg = '0';    
            if($myclass->create()){
                $bag_id_acc = ($myclass->bag_id())?$myclass->bag_id:"0";
                http_response_code(200);
                echo json_encode(array("message" => "Bag_no was created.", "bag_id" => $bag_id_acc));

            }else{
                http_response_code(400); 
                echo json_encode(array("message" => "Bag_no can not Create."));
            }    

        }else if($acc == "upKg"){ //ปรับปรุงแก้ไขข้อมูล
            $myclass->bag_id = $data->bag_id;
            $myclass->bag_kg = $data->bag_kg;
            if( !empty($myclass->bag_id) && $myclass->updateKg()){                
                    http_response_code(200);
                    echo json_encode(array("message" => "Kg was update."));                
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to update Kg."));
            }

        }else if(!empty($acc) && $acc == "del"){ //ลบข้อมูล           
            $myclass->bag_id = $data->bag_id;
            $delclass->bag_id = $data->bag_id;
           if(!empty($myclass->bag_id) && $myclass->delete()){  
               $delclass->delete_bagid();                             
                http_response_code(200);
                echo json_encode(array("message" => "Bag was delete."));                
           }else{
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to delete Bag."));
           }   

        }else if($acc == "exit"){ //ตรวจสอบว่ามีซ้ำหรือไม่           
            $myclass->bag_no = strtoupper($data->bag_no);
            $myclass->pack_date = DateYmd($data->pack_date);
            $myclass->pack_bill = $data->pack_bill;
            $myclass->packer_id = $data->packer_id;
            
           if($myclass->bagno_Exit()){                               
                http_response_code(200);
                echo json_encode(array("message" => "Bag no is exits."));                              
           }else{
                http_response_code(200); 
                echo json_encode(array("message" => "Bag no is not exits."));  
           }   

        }else{
            http_response_code(400); 
            echo json_encode(array("message" => "Unable to access Bag."));
        }
    }
    
    catch (Exception $e){    // if decode fails, it means jwt is invalid
        http_response_code(401);    
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}else{  // show error message if jwt is empty
    http_response_code(401); 
    echo json_encode(array("message" => "Access denied.."));
}

function DateYmd($date){
    $get_date = explode("/", $date);
    return $get_date['2'] . "-" . $get_date['1'] . "-" . $get_date['0'];
}





?>

