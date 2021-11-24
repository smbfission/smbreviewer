<?php
@session_start();
    ini_set('max_execution_time','9000000');
   include("database.php");
$sql="select version from settings limit 1";
$res=mysqli_query($con,$sql);
$row=mysqli_fetch_array($res);
$old_ver=$row['version'];
if(!isset($_GET['ver']))
{
$ver=$old_ver; 
}
else
$ver=$_GET['ver'];
$url=getServerURL();
$time=time();
echo '<body style="width:600px; margin:0 auto;"><center><img src="assets/img/ajax-loader-black-bar.gif" style="margin-top: 100;width: 100px;"><br><br><h2>Please wait....</h2><hr></center></body>';
 $json=file_get_contents("https://updates.ranksol.com/app_updates/reviews_updates/update.php?url=$url&ver=$ver&time=$time");
     ///////////////log mysqli file 
      if(isset($_GET['log']) && $_GET['log'] == "true"){
          echo "<b>Json recieved</b>".$json."<b>Version---$old_ver</b><hr>";
      }
$arr=json_decode($json,true);
if($arr['error'] == "no")
{
if(is_array($arr['sql']) && count($arr['sql'])>0){
  //  print_r($arr['sql']);
   // die();

foreach($arr['sql'] as $key => $val){
 
  $file= @file_get_contents("https://updates.ranksol.com/app_updates/reviews_updates/sql/$val?time=$time");
$queryArray = array();
$queryArray = explode(';',$file);
for($i=0;$i<count($queryArray);$i++)
    if(trim($queryArray[$i])!='')
    mysqli_query($con,$queryArray[$i]); 
    ///////////////log mysqli file 
      if(isset($_GET['log']) && $_GET['log'] == "true"){
    echo "<b>My sql contents------</b>".$file."------<hr>";  
  }
  ////////////////////end log 
}
}
///echo "https://updates.ranksol.com/app_updates/reviews_updates/update/$arr[zip]";
if(strlen($arr['zip'])> 3)
{
file_put_contents($arr['zip'], file_get_contents("https://updates.ranksol.com/app_updates/reviews_updates/update/$arr[zip]?time=$time"));
if(class_exists('ZipArchive'))
{
$dir=dirname(__FILE__);
$zip = new ZipArchive;
$res = $zip->open("$arr[zip]");
if ($res === TRUE) {
 //   echo 'ok';
    $zip->extractTo("$dir/");
    $zip->close();
    ///////////////////log zip
    if(isset($_GET['log']) && $_GET['log'] == "true"){
    echo "<b>Zip------</b>".$arr['zip']."------<hr>";  
  }
  ///////////////////end log zip///////////////// 
} else {
    echo 'failed, code:' . $res;
}
}
else{ 
include_once('pclzip.lib.php');
  $archive = new PclZip($arr['zip']);
  $v_list=$archive->extract();
    if ($v_list == 0) {
    die("Error : ".$archive->errorInfo(true));
  }
      ///////////////////log zip lib
    if(isset($_GET['log']) && $_GET['log'] == "true"){
    echo "<b>Zip lib------</b>".$arr['zip']."------<hr>";  
  }
  ///////////////////end log zip///////////////// 
}
@unlink($arr['zip']);
}
if(is_array($arr['del']) && count($arr['del'])>0)
{ foreach($arr['del'] as $val_d)
 {@unlink($val_d); 
    ///////////////////log unlink
    if(isset($_GET['log']) && $_GET['log'] == "true"){
    echo "<b>unlink------</b>".$val_d."------<hr>";  
  }
  ///////////////////end log unlink///////////////// 
 }   
 }
 include("database.php");
//$con_db = mysqli_connect($hostname, $username, $password) or die(mysqli_error());
//mysqli_select_db($database, $con_db);
//$sql="update twilio_settings set wbsms_version='$arr[version]'";
if(isset($arr['version']) && $arr['version'] !="")
{$sql="update settings set version='".$arr['version']."'";
mysqli_query($con,$sql);
echo "<h2>".$_SESSION['msg'] =  "Application Updated Successfully";
}
}



sleep(3);
if(!isset($_GET['log']))
{?>
<script>window.location.href="campaign.php"</script>
<?php
}
function getServerURL()
{
$serverName = $_SERVER['SERVER_NAME'];
$filePath = $_SERVER['REQUEST_URI'];
$withInstall = substr($filePath,0,strrpos($filePath,'/')+1);
$serverPath = $serverName.$withInstall;
$applicationPath = $serverPath;

if(strpos($applicationPath,'http://www.')===false)
{
if(strpos($applicationPath,'www.')===false)
$applicationPath = 'www.'.$applicationPath;
if(strpos($applicationPath,'http://')===false)
$applicationPath = 'http://'.$applicationPath;
}

//$url = $applicationPath.'uploads/';

$applicationPath = str_replace(array("http://","https://"),"",$applicationPath);
return $applicationPath;
}
?>
