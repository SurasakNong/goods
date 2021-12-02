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
include_once 'objects/product.php';

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

$myclass = new Product($db);
$data = json_decode(file_get_contents("php://input"));
$acc=isset($data->acc) ? $data->acc : "";
$jwt=isset($data->jwt) ? $data->jwt : "";
if($jwt){ 
    // if decode succeed, show user details
    try { 
        $decoded = JWT::decode($jwt, $key, array('HS256'));   // decode jwt
        if(!empty($acc) && $acc == "add"){ //ทำการเพิ่มข้อมูล
            $myclass->code = $data->code;
            $myclass->dia = $data->dia;
            $myclass->color = $data->color;
            $myclass->knot = $data->knot;
            $myclass->ms = $data->ms;
            $myclass->ms_unit = $data->ms_unit;
            $myclass->md = $data->md;
            $myclass->md_unit = $data->md_unit;
            $myclass->ml = $data->ml;
            $myclass->ml_unit = $data->ml_unit;
            $myclass->label = $data->label;
            $myclass->pcs = $data->pcs;
            $myclass->wt = $data->wt;
            $myclass->search = $data->searchtxt;

            if( !empty($myclass->code)){
                if($myclass->codeExists()){
                    http_response_code(400); 
                    echo json_encode(array("message" => "Code Exit."));
                }else {
                    $myclass->create();
                    http_response_code(200);
                    echo json_encode(array("message" => "Code was created."));
                }
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to create Code."));
            } 
        }else if(!empty($acc) && $acc == "up"){ //ปรับปรุงแก้ไขข้อมูล
            $myclass->id = $data->id_prod;
            $myclass->code = $data->code;
            $myclass->dia = $data->dia;
            $myclass->color = $data->color;
            $myclass->knot = $data->knot;
            $myclass->ms = $data->ms;
            $myclass->ms_unit = $data->ms_unit;
            $myclass->md = $data->md;
            $myclass->md_unit = $data->md_unit;
            $myclass->ml = $data->ml;
            $myclass->ml_unit = $data->ml_unit;
            $myclass->label = $data->label;
            $myclass->pcs = $data->pcs;
            $myclass->wt = $data->wt;
            $myclass->search = $data->searchtxt;

            if( !empty($myclass->code)){
                if($myclass->newcodeExit()){
                    http_response_code(400); 
                    echo json_encode(array("message" => "Code Exit."));
                }else {
                    $myclass->update();
                    http_response_code(200);
                    echo json_encode(array("message" => "Code was update."));
                }
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to update Code."));
            }

        }else if(!empty($acc) && $acc == "del"){ //ลบข้อมูล           
            $myclass->id = $data->id;
           if(!empty($myclass->id) && $myclass->delete()){                               
                http_response_code(200);
                echo json_encode(array("message" => "Code was delete."));                
           }else{
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to delete Code."));
           }   

        }else{
            http_response_code(400); 
            echo json_encode(array("message" => "Unable to access Code."));
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

