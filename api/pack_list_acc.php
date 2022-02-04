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
include_once 'objects/pack_list.php';
include_once 'objects/product.php';

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

$myclass = new Packlist($db);
$myclass_prod = new Product($db);
$data = json_decode(file_get_contents("php://input"));
$acc= $data->acc;
$jwt= $data->jwt;
if($jwt){ 
    try { 
        $decoded = JWT::decode($jwt, $key, array('HS256'));   // decode jwt
        if($acc == "add"){ //ทำการเพิ่มข้อมูล
            $myclass_prod->code = $data->code;   
            if( !empty($myclass_prod->code)){                
                if($myclass_prod->codeExists()){
                    $myclass->bag_id = $data->bag_id;
                    $myclass->list_nt = $data->code;                     
                    $myclass->list_pcs = ($data->ck_pcs == "1")?$data->pcs:$myclass_prod->pcs;
                    $myclass->list_kg = $myclass_prod->wt * $myclass->list_pcs;
                    $myclass->create();
                    http_response_code(200);
                    echo json_encode(array("message" => "Pack list was created."));
                }else{
                    http_response_code(400); 
                    echo json_encode(array("message" => "Not found code."));
                }   

            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to create list."));
            }

        }else if($acc == "up"){ //ปรับปรุงแก้ไขข้อมูล
            /*$myclass->id = $data->id_data;
            $myclass->pcs = $data->ed_data;

            if( !empty($myclass->id)){
                    $myclass->update();
                    http_response_code(200);
                    echo json_encode(array("message" => "Receive was update."));
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to update receive."));
            }*/

        }else if($acc == "del"){ //ลบข้อมูล           
            $myclass->list_id = $data->list_id;
           if(!empty($myclass->list_id) && $myclass->delete()){                               
                http_response_code(200);
                echo json_encode(array("message" => "List was delete."));                
           }else{
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to delete List."));
           }  

        }else{
            http_response_code(400); 
            echo json_encode(array("message" => "Unable to access list."));
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


?>

