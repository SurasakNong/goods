<?php
// show error reporting
error_reporting(E_ALL);
 
// set your default time-zone
date_default_timezone_set('Asia/Bangkok');
 
//=========== variables used for jwt ============================================
$key = "surasak_123_iloveyou";
$issued_at = time();
$expiration_time = $issued_at + (60 * 60 * 4); // valid for 4 hour
$issuer = "surasak";

//============ variables ========================================================
$home = "http://192.168.50.230:8092/goods";
$api_key_value = "surasak_iamserm";

?>