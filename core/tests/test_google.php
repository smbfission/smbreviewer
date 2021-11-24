<?php
@session_name('__ggl_smb');
@session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log(print_r($_REQUEST,true));



$client_id= "420944556197-pmcah2al3lrmm6fn1cn5c3kgnpu2c2e7.apps.googleusercontent.com";
$client_secret= "Gu6wYWSQUlX0b4aa6BkZumbP";

$redirect_uri = 'https://reviews.smbreviewer.com/core/tests/test_google.php';
//$access_token="ya29.a0Ae4lvC0wRKUO6g8tLA2KqJTut_DeTXVhtyEcTIBXVMzlcDeWshK1-MlE4zGut-KV9_KG4dVjztPdPFsV4k9BJvID4VSIEKKnmiyXDK_Xsk28NfuKoeZoaOH53obpzZfy-xQd-b-tERp17tvyphLMOfbj7_Mm66O5AAQ";

$_SESSION['__ggl_rt']="1//09abyRI6Hi1QTCgYIARAAGAkSNwF-L9IrgqFevn1SBeQgjZ3JBvqtjQmB79sdgj_AlktrHcW_oNiJWDbMIPbF1HWOE1jfXjQTb4E";


$data = [
  // "scope" => "https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/spreadsheets https://www.googleapis.com/auth/drive.metadata.readonly",
         "scope" => "https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/spreadsheets",
         "client_id"=>$client_id,
         "access_type"=>"offline",
         "prompt"=>"consent",  // without this paramtere no refresh_token in response
         "state"=>"ggl_rsp_rcvd",
         "include_granted_scopes"=>"true",
         "response_type"=>"code",
         "redirect_uri" => $redirect_uri,
       ];
$login_url ="https://accounts.google.com/o/oauth2/v2/auth?".http_build_query($data);



if (isset($_REQUEST['state']) && $_REQUEST['state']=="ggl_rsp_rcvd") {

$url ="https://oauth2.googleapis.com/token";
$data = ["code" => $_REQUEST['code'],
         "client_id"=>$client_id,
         "client_secret"=>$client_secret,
         "grant_type"=>"authorization_code",
         "redirect_uri" => $redirect_uri,
       ];
       $res = post_fb($url,"post",http_build_query($data));

if (isset(json_decode($res,true)['access_token']) && isset(json_decode($res,true)['refresh_token'])) {
      $_SESSION['__ggl_at'] = json_decode($res,true)['access_token'];
      $_SESSION['__ggl_rt'] = json_decode($res,true)['refresh_token'];

      $login_url = '';
      header('Location: '.$_SERVER['PHP_SELF']);

    // header('Content-Type: application/json');

  } else {

    unset($_SESSION['__ggl_at']);
    unset($_SESSION['__ggl_rt']);
  }
}

if (isset($_SESSION['__ggl_at'])) {
  $url ="https://www.googleapis.com/userinfo/v2/me";
  $res = post_fb($url,"get","",$_SESSION['__ggl_at']);
    if (!isset(json_decode($res,true)['id'])) {
      unset($_SESSION['__ggl_at']);
    } else {
      $login_url='';
    }
}


if (isset($_SESSION['__ggl_rt'])) {

    $url ="https://oauth2.googleapis.com/token";
    $data = [
             "client_id"=>$client_id,
             "client_secret"=>$client_secret,
             "grant_type"=>"refresh_token",
             "refresh_token" => $_SESSION['__ggl_rt'],
           ];
           $res = post_fb($url,"post",http_build_query($data));

      if (isset(json_decode($res,true)['access_token'])) {
        $_SESSION['__ggl_at'] = json_decode($res,true)['access_token'];
        $login_url = '';
      }
      else {
        unset($_SESSION['__ggl_rt']);
      }
}

if (isset($_SESSION['__ggl_at'])) {
  $url ="https://www.googleapis.com/userinfo/v2/me";
  $res = post_fb($url,"get","",$_SESSION['__ggl_at']);
  if (!isset(json_decode($res,true)['id'])) {
    unset($_SESSION['__ggl_at']);
  } else {
    $login_url='';
  }
}


?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>

    <?php if ($login_url!=''): ?>

     <a class="btn  btn-fill btn-primary " href="<?= $login_url ?>"><span class="ace-icon fa fa-plug bigger-120"></span> Connect </a>

    <?php else: ?>

      <?php
      echo "<pre>";
      echo $res ;
      echo "</pre>";
      // $url ="https://sheets.googleapis.com/v4/spreadsheets/1SwO0RJu23bGiSVGJwkpJFanQq69pqlALHw5bl2-YU_U";
    //  $url ="https://www.googleapis.com/drive/v3/files?q=mimeType='application/vnd.google-apps.spreadsheet'";
      // $url ="https://mybusiness.googleapis.com/v4/accounts/";

      $url ="https://www.googleapis.com/userinfo/v2/me";
      $res = post_fb($url,"get","",$_SESSION['__ggl_at']);

      echo "<pre>";
      echo $res ;
      echo "</pre>";

     ?>



    <?php endif; ?>



<?php



function post_fb($url, $method="get", $body="", $apiKey="")
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    if ($apiKey!="") {
        $headr[] = 'Authorization: Bearer '.$apiKey;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
    }

    if ($method == "post") {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));


    } elseif ($method=="delete") {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    } else {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    }
    return curl_exec($ch);
}


 ?>
</body>
</html>
