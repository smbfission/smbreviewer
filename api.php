<?php
ob_start();

@session_start();
//include_once("header.php");


require_once('core/database.php');

$db = new Database();

$row = $db->getUser(@$_SESSION['user_id']);


if(isset($_REQUEST['reset']) && $_REQUEST['reset']=='reset'){

$sql = "UPDATE `user` SET `app_id` = NULL, `app_secret`= NULL, `access_token` = NULL where `id` = '".$_SESSION['user_id']."'";
mysqli_query($conn,$sql);

  header('Location: /settings.php');
  die();
}



if(isset($_REQUEST['disconnect']) ){
   $revoke_url="https://graph.facebook.com/".$_REQUEST['disconnect']."/permissions?access_token=".$row['access_token'];
   $res=post_fb($revoke_url,"delete");
   $sql = "UPDATE `user` SET  `access_token` = NULL where `id` = '".$_SESSION['user_id']."'";
   mysqli_query($conn,$sql);

  header('Location: /settings.php');
  die();
}



if(isset($_REQUEST['app_id']) && isset($_REQUEST['app_secret'])){

    $sql = "UPDATE `user` set  `app_id` = case when `advanced_settings`=1 then  '".$_REQUEST['app_id']."' else NULL end , `app_secret` = case when `advanced_settings`=1 then '".$_REQUEST['app_secret']."' else NULL end where `id` = '".$_SESSION['user_id']."'";
    mysqli_query($conn,$sql);
    header('Location: /settings.php');
    die();

    // $app_id = $_REQUEST['app_id'];
    // $fb_secret = $_REQUEST['app_secret'];

}


//$sql = "select * from user where id = '".$_SESSION['user_id']."'";


$app_id = @$row['app_id'];
$fb_secret = @$row['app_secret'];




$redirect_url = getServerURL().'api.php';

$redirect_url = "https://".str_replace(array("http://","https://","www."),"",$redirect_url);




if(isset($_GET['code']) && $_GET['code']!="")
{

    $code = $_GET['code'];

    if ($row['id']==169) {
      // $app_id="481675452476366";
      // $fb_secret = "aacd7bd53c2e7b642ba9e412a503b759";

    }
    //$redirect_url = getServerURL().'api.php';
    //$redirect_url= urlencode($redirect_url);

    $token_url="https://graph.facebook.com/oauth/access_token?client_id=$app_id&redirect_uri=$redirect_url&type=token&client_secret=$fb_secret&code=$code";


    $access_token=post_fb($token_url,"get");
    //$fb_success=json_decode($access_token,true) == NULL ? "token" : "error";
    $fb_success=json_decode($access_token,true);


    /*
    echo "<pre>";
    print_r($access_token);
    print_r($fb_success);
    echo "</pre>";
    die();
    */

    if(is_array($fb_success) && @$fb_success['access_token'] != "")
    {

        // $url_me="https://graph.facebook.com/me?access_token=".$fb_success['access_token'];
        // $me=post_fb($url_me,"get");
        //$sql = "update user set access_token = '".$access_token."' where id = '".$_SESSION['user_id']."'";
        $sql = "update user set access_token = '".$fb_success['access_token']."' where id = '".$_SESSION['user_id']."'";
        mysqli_query($conn,$sql);
    }
    else{


        $_SESSION['msg'] = "Error While Connected to Facebook API, Please verify your app credentials and try again.";
        ?>
        <script>window.location = 'settings.php'; </script>
        <?php
        die();
        exit;

    }

    $_SESSION['msg'] = "Application Connected Successfully."."";
    ?>
    <script>window.location = 'settings.php'; </script>
    <?php
    die();
    exit;
}

/********************************/
/********************************/

function post_fb($url,$method,$body=""){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if($method == "post"){
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    elseif ($method == "delete") {

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    else{
        curl_setopt($ch, CURLOPT_HTTPGET, true );
    }
    return curl_exec($ch);
}


function post_fb2($url,$method,$body=""){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if($method == "post"){
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    }
    else{
        curl_setopt($ch, CURLOPT_HTTPGET, true );
    }
    return curl_exec($ch);
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
    $applicationPath = str_replace("www.","",$applicationPath);
    return $applicationPath;
}
?>
