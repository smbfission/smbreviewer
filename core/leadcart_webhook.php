<?php



  header('Content-Type: application/json');

  $res = ['success'=>false,'message'=>'Something went wrong'];
  $res = ['success'=>true,'message'=>'Done'];

  error_log(print_r($_POST,true));

  die(json_encode($res,true));



?>
