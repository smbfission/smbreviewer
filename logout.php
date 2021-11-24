<?php
@session_start();
unset($_SESSION);
session_destroy();
$_SESSION['error'] = "Successfully Logged out";
header("location:/");
die();
?>
