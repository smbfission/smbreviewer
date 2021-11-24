<?php
header('Content-Type: application/json');
// header('Access-Control-Allow-Origin: *');

if($json = json_decode(file_get_contents("php://input"), true)) {
     //print_r($json);

     $data = $json;

     error_log('json !!!'.print_r($data,true));
 } else {
    // print_r($_POST);
     $data = $_POST;
     error_log('post !!!'.print_r($data,true));
  }

  	die(json_encode(["success"=>true],true));




?>
