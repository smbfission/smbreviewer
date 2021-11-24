
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title></title>


<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

@session_start();
require_once('../../core/database.php');
require_once('../../core/tools.php');
$db= new Database();

$foo = $db->updateCustomReviewsImages(31);

print_r($foo);

 ?>


</body>
</html>
