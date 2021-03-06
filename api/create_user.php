<?php
include_once 'config/core.php';
// required headers
header("Access-Control-Allow-Origin: ${home}");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/database.php';
include_once 'objects/user.php';
 
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$data = json_decode(file_get_contents("php://input"));
$user->firstname = $data->firstname;
$user->lastname = $data->lastname;
$user->depart = $data->depart;
$user->username = $data->username;
$user->type = "0";
$user->password = "123456";


if( 
    !empty($user->firstname) &&
    !empty($user->lastname) &&
    !empty($user->depart)
){
    
    if($user->usernameExists()){
        http_response_code(400); 
        echo json_encode(array("message" => "Username Exit."));
    }else {
        $_usercreate =  $user->create();
        http_response_code(200);
        echo json_encode(array("message" => "User was created."));
    }
}else { // message if unable to create user
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create user."));
}

?>

