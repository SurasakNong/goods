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
include_once 'objects/group.php';

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

$myclass = new Group($db);
$data = json_decode(file_get_contents("php://input"));
$acc=isset($data->acc) ? $data->acc : "";
$jwt=isset($data->jwt) ? $data->jwt : "";
if($jwt){ 
    // if decode succeed, show user details
    try { 
        $decoded = JWT::decode($jwt, $key, array('HS256'));   // decode jwt
        if(!empty($acc) && $acc == "add"){ //ทำการเพิ่มข้อมูล
            $myclass->bd_id = $data->bd_id;
            $myclass->group_name = $data->group_name;
            $myclass->group_mc = $data->group_mc;
            $myclass->group_rpm = $data->group_rpm;
            if( !empty($myclass->group_name)){
                if($myclass->nameExists()){
                    http_response_code(400); 
                    echo json_encode(array("message" => "Group Exit."));
                }else {
                    $myclass->create();
                    http_response_code(200);
                    echo json_encode(array("message" => "Group was created."));
                }
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to create Group."));
            } 
        }else if(!empty($acc) && $acc == "up"){ //ปรับปรุงแก้ไขข้อมูล
            $myclass->group_id = $data->group_id;
            $myclass->bd_id = $data->bd_id;
            $myclass->group_name = $data->group_name;
            $myclass->group_mc = $data->group_mc;
            $myclass->group_rpm = $data->group_rpm;

            if( !empty($myclass->group_name)){
                if($myclass->newnameExit()){
                    http_response_code(400); 
                    echo json_encode(array("message" => "Group Exit."));
                }else {
                    $myclass->update();
                    http_response_code(200);
                    echo json_encode(array("message" => "Group was update."));
                }
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to update Group."));
            }

        }else if(!empty($acc) && $acc == "del"){ //ลบข้อมูล           
            $myclass->group_id = $data->id;
           if(!empty($myclass->group_id) && $myclass->delete()){                               
                http_response_code(200);
                echo json_encode(array("message" => "Group was delete."));                
           }else{
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to delete Group."));
           }   

        }else{
            http_response_code(400); 
            echo json_encode(array("message" => "Unable to access Group."));
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

