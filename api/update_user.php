<?php
include_once 'config/core.php';
// required headers
header("Access-Control-Allow-Origin: ${home}");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';
  
// get database connection
$database = new Database();
$db = $database->getConnection(); 
// instantiate user object
$user = new User($db); 
// get posted data
$data = json_decode(file_get_contents("php://input")); 
// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";
 
// if jwt is not empty
if($jwt){
    try { 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256')); 
        // set user property values
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->depart = $data->depart;
        $user->username = $data->username;
        $user->newpassword = $data->newpassword;
        $user->id = $decoded->data->id;
        $user->type = $decoded->data->type;

        if($user->newUsernameExit()){
            http_response_code(400); 
            echo json_encode(array("message" => "Username Exit."));
        }else if(!empty($data->newpassword)){
                if($user->usernameExists() && password_verify($data->password, $user->password)){
                    if($user->update()){
                        $token = array( // we need to re-generate jwt because user details might be different
                            "iat" => $issued_at,
                            "exp" => $expiration_time,
                            "iss" => $issuer,
                            "data" => array(
                                "id" => $user->id,
                                "firstname" => $user->firstname,
                                "lastname" => $user->lastname,
                                "username" => $user->username,
                                "depart" => $user->depart,
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

        }else{
            if($user->update()){
                $token = array( // we need to re-generate jwt because user details might be different
                    "iat" => $issued_at,
                    "exp" => $expiration_time,
                    "iss" => $issuer,
                    "data" => array(
                        "id" => $user->id,
                        "firstname" => $user->firstname,
                        "lastname" => $user->lastname,
                        "username" => $user->username,
                        "depart" => $user->depart,
                        "type" => $user->type
                    )
                );
                $jwt = JWT::encode($token, $key);            
                // set response code
                http_response_code(200);            
                // response in json format
                echo json_encode(
                        array(
                            "message" => "User was updated.",
                            "jwt" => $jwt,
                            "type" => $user->type,
                        )
                    );
            }else{
                // set response code
                http_response_code(401);        
                // show error message
                echo json_encode(array("message" => "Unable to update user."));
            }                 

        }

    }
 
    // if decode fails, it means jwt is invalid
    catch (Exception $e){    
        // set response code
        http_response_code(401);    
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
 
// show error message if jwt is empty
else{ 
    // set response code
    http_response_code(401); 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}
?>