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
include_once 'objects/user.php';

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$data = json_decode(file_get_contents("php://input"));
$acc=isset($data->acc) ? $data->acc : "";
$jwt=isset($data->jwt) ? $data->jwt : "";
if($jwt){ 
    // if decode succeed, show user details
    try { 
        $decoded = JWT::decode($jwt, $key, array('HS256'));   // decode jwt
        if(!empty($acc) && $acc == "add"){ //ทำการเพิ่มข้อมูล
            $user->firstname = $data->firstname;
            $user->lastname = $data->lastname;
            $user->depart = $data->depart;
            $user->username = $data->username;
            $user->type = $data->type; 
            $user->password = $data->password; 

            if( !empty($user->username)){
                if($user->usernameExists()){
                    http_response_code(400); 
                    echo json_encode(array("message" => "Username Exit."));
                }else {
                    $user->create();
                    http_response_code(200);
                    echo json_encode(array("message" => "User was created."));
                }
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to create User."));
            } 
        }else if(!empty($acc) && $acc == "up"){ //ปรับปรุงแก้ไขข้อมูล
            $user->id = $data->id;
            $user->firstname = $data->firstname;
            $user->lastname = $data->lastname;
            $user->depart = $data->depart;
            $user->username = $data->username;
            $user->type = $data->type;            
            $user->newpassword = $data->newpassword;

            if( !empty($user->username) && !empty($user->firstname)){
                if($user->newUsernameExit()){
                    http_response_code(400); 
                    echo json_encode(array("message" => "Username Exit."));
                }else if(!empty($data->newpassword) && ($data->password !== "nong_reset")){ // กรณีกำหนดรหัสผ่านใหม่ และ ระบุรหัสผ่านเก่า
                        if($user->usernameExists() && password_verify($data->password, $user->password)){
                            if($user->update()){
                                $token = array( // we need to re-generate jwt
                                    "iat" => $issued_at,
                                    "exp" => $expiration_time,
                                    "iss" => $issuer,
                                    "data" => array(
                                        "id" => $user->id,
                                        "firstname" => $user->firstname,
                                        "lastname" => $user->lastname,
                                        "username" => $user->username,
                                        "depart" => $user->depart,
                                        "departname" => $user->dp_name,
                                        "type" => $user->type
                                    )
                                );
                                $jwt = JWT::encode($token, $key);
                                http_response_code(200);
                                echo json_encode(
                                        array(
                                            "message" => "User was updated.",
                                            "jwt" => $jwt,
                                            "type" => $user->type,
                                        )
                                    );
                            }else{
                                http_response_code(401);
                                echo json_encode(array("message" => "Unable to update user."));
                            }
                        }else{
                            http_response_code(400); 
                            echo json_encode(array("message" => "Invalid password."));
                        }

                    }else if(!empty($data->newpassword) && ($data->password === "nong_reset")){ // กรณีกำหนดรหัสผ่านใหม่ สำหรับ admin รีเซท
                        if($user->usernameExists()){
                            if($user->update()){
                                $token = array( // we need to re-generate jwt
                                    "iat" => $issued_at,
                                    "exp" => $expiration_time,
                                    "iss" => $issuer,
                                    "data" => array(
                                        "id" => $user->id,
                                        "firstname" => $user->firstname,
                                        "lastname" => $user->lastname,
                                        "username" => $user->username,
                                        "depart" => $user->depart,
                                        "departname" => $user->dp_name,
                                        "type" => $user->type
                                    )
                                );
                                $jwt = JWT::encode($token, $key);
                                http_response_code(200);
                                echo json_encode(
                                        array(
                                            "message" => "User was updated.",
                                            "jwt" => $jwt,
                                            "type" => $user->type,
                                        )
                                    );
                            }else{
                                http_response_code(401);
                                echo json_encode(array("message" => "Unable to update user."));
                            }
                        }else{
                            http_response_code(400); 
                            echo json_encode(array("message" => "Invalid password."));
                        }

                }else{ // กรณีไม่กำหนดรหัสผ่านใหม่ ช่องรหัสปล่อยว่าง
                    if($user->update()){
                        $token = array( // we need to re-generate jwt
                            "iat" => $issued_at,
                            "exp" => $expiration_time,
                            "iss" => $issuer,
                            "data" => array(
                                "id" => $user->id,
                                "firstname" => $user->firstname,
                                "lastname" => $user->lastname,
                                "username" => $user->username,
                                "depart" => $user->depart,
                                "departname" => $user->dp_name,
                                "type" => $user->type
                            )
                        );
                        $jwt = JWT::encode($token, $key);
                        http_response_code(200);
                        echo json_encode(
                                array(
                                    "message" => "User was updated.",
                                    "jwt" => $jwt,
                                    "type" => $user->type,
                                )
                            );
                    }else{
                        http_response_code(401);
                        echo json_encode(array("message" => "Unable to update user."));
                    }                   

                }
            }else{ 
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to update User."));
            }

        }else if(!empty($acc) && $acc == "del"){ //ลบข้อมูล           
            $user->id = $data->id;
           if(!empty($user->id) && $user->delete()){                               
                http_response_code(200);
                echo json_encode(array("message" => "User was delete."));                
           }else{
                http_response_code(400); 
                echo json_encode(array("message" => "Unable to delete User."));
           }   

        }else{
            http_response_code(400); 
            echo json_encode(array("message" => "Unable to access User."));
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

