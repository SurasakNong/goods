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
include_once 'objects/receive.php';
include_once 'objects/product.php';

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

$myclass = new Receive($db);
$myclass_prod = new Product($db);
$data = json_decode(file_get_contents("php://input"));
$acc= $data->acc;
$jwt= $data->jwt;
if($jwt){ 
    // if decode succeed, show user details
    try { 
        $decoded = JWT::decode($jwt, $key, array('HS256'));   // decode jwt
        if(!empty($acc) && $acc == "add"){ //ทำการเพิ่มข้อมูล
            $myclass_prod->code = $data->code;   
            if( !empty($myclass_prod->code)){
                
                if($myclass_prod->codeExists()){
                    $myclass->date_rec = DateYmd($data->date_rec);
                    $myclass->bill = $data->bill;
                    $myclass->dp_rec = $data->dp_rec;
                    $myclass->dp_post = $data->dp_post;
                    $myclass->code = $data->code;                     
                    $myclass->pcs = ($data->ck_pcs == "1")?$data->pcs:$myclass_prod->pcs;
                    $myclass->wt = $myclass_prod->wt;
                    $myclass->create();
                    http_response_code(200);
                    echo json_encode(array("message" => "Receive was created."));
                }else{
                    http_response_code(400); 
                    echo json_encode(array("message" => "Not found code."));
                }              

            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to create receive."));
            } 

        }else if(!empty($acc) && $acc == "up"){ //ปรับปรุงแก้ไขข้อมูล
            $myclass->id = $data->id_data;
            $myclass->pcs = $data->ed_data;

            if( !empty($myclass->id)){
                    $myclass->update();
                    http_response_code(200);
                    echo json_encode(array("message" => "Receive was update."));
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to update receive."));
            }

        }else if(!empty($acc) && $acc == "del"){ //ลบข้อมูล           
            $myclass->id = $data->id;
           if(!empty($myclass->id) && $myclass->delete()){                               
                http_response_code(200);
                echo json_encode(array("message" => "Receive was delete."));                
           }else{
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to delete receive."));
           }   

        }else{
            http_response_code(400); 
            echo json_encode(array("message" => "Unable to access receive."));
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
    echo json_encode(array("message" => "Access denied."));
}

function DateYmd($date)
{
    $get_date = explode("/", $date);
    return $get_date['2'] . "-" . $get_date['1'] . "-" . $get_date['0'];
}

?>

