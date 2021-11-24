<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!extension_loaded('Zend OPcache')) {
    echo '<div style="background-color: #F2DEDE; color: #B94A48; padding: 1em;">You do not have the Zend OPcache extension loaded, sample data is being shown instead.</div>';

} else {
  echo"opcache";
}

if(class_exists('Memcache')){
echo "yaaaa!";
} else {
echo "nononon";

}


$Memcached = new Memcached();
$Memcached->addServer('localhost', 11211);
$Memcached->set('key', false);
var_dump($Memcached->get('key'));      // boolean false
var_dump($Memcached->getResultCode()); // int 0 which is  Memcached::RES_SUCCESS 

 // $m->addServer('localhost', 11211);
//
// $m->set('int', 99);
// $m->set('string', 'a simple string');
// $m->set('array', array(11, 12));
// /* expire 'object' key in 5 minutes */
// $m->set('object', new stdclass, time() + 300);
//
// echo "<br>";
// echo "<br>";
// echo "<br>";
// var_dump($m->get('int'));
// var_dump($m->get('string'));
// var_dump($m->get('array'));
// var_dump($m->get('object'));
//
// require_once('../../../database.php');
// $db = new Database();
// $u = $db->getUser(1);
// $u = $db->getUserCampaignsByUserId(1);
// $u = $db->getUserCampaignsCount(1);
// $u = $db->getCampaignByUserIdCampaignId(25,61);
//
// print_r($u);

 ?>
