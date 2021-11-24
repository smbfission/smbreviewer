<?php

@session_start();
include_once('core/database.php');
$pkg_id = $_REQUEST['pkg_id'];

$db= new Database();
// $sql = "select * from plans where id='".$pkg_id."'";
//
// $res = mysqli_query($con,$sql);
// $plan = mysqli_fetch_assoc($res);

$plan=$db->getPlanById($pkg_id);


$settings = $db->getGeneralSettings();

$paypalUrl		 = 'https://www.paypal.com/cgi-bin/webscr';
$paypalEmail	 = $settings['business_email'];

/*$paypalUrl		 = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
$paypalEmail	 = 'business@nimblewebsolutions.com';*/

$returnURL    = getServerUrl();
$returnURL    = substr($returnURL,0,strrpos($returnURL,'/'));

$webUserID = "";
$pkgPrice = $_REQUEST['pkg_price'];
$password = $_REQUEST['password'];
$uid = @$_REQUEST['uid'];
$rePassword = $_REQUEST['retype_password'];
if($password==$rePassword){

if(!$db->getUserByEmail($_REQUEST['email'])){
	error_log('user exists');
}

	$check = "select id from user where email='".$_REQUEST['email']."'";
	$resep = mysqli_query($con,$check);


	if(mysqli_num_rows($resep)==0){

		$sql = "insert into web_user_info
            (pkg_id,name,email,password)
            values
			('".$_REQUEST['pkg_id']."','".$_REQUEST['name']."','".$_REQUEST['email']."','".$password."')";
		$res = mysqli_query($con,$sql);

		if($res){
            $webUserID = mysqli_insert_id($con);
            if($plan['amount'] == 0){
                header("location: /ipn.php?custom=".$webUserID."&txn_type=subscr_payment&subscr_id=46464as6dbavhgvcac&item_name=".$plan['title'].
								"&payment_status=completed&payer_email=".$_REQUEST['email'].
								 "&payer_phone=".$_REQUEST['phone'].
								 "&address=".$_REQUEST['address'].
								"&txn_id=null");
                die();
            }
			?>

            <form id="paypalForm" action="<?php echo $paypalUrl?>" method="post" onsubmit="$('ccSubmit').style.display='none';$('ccSubmitSpinner').style.display='block';">
        		<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
        		<input type="hidden" name="cmd" value="_xclick-subscriptions">
        	    <input type="hidden" name="business" value="<?=$paypalEmail?>">
        		<!--<input type="hidden" name="business" value="business@nimblewebsolutions.com" />  -->
        		<input type="hidden" name="first_name" value="<?=$_REQUEST['name']?>">
        		<!-- <input type="hidden" name="last_name" value=""> -->
        		<input type="hidden" name="item_name" value="<?=$plan['title']?>">
                <input type="hidden" name="no_of_campaigns" value="<?= $plan['no_of_campaigns'] ?>">
        		<input type="hidden" name="item_number" value="<?=$plan['id']?>">
        		<input type="hidden" name="invoice" value="<?=$webUserID.'_'.strtotime(date("Y-m-d H:i:s"));?>">
        		<input type="hidden" name="no_shipping" value="1">
        		<input type="hidden" name="no_note" value="1">
        		<input type="hidden" name="currency_code" value="USD">
        		<input type="hidden" name="lc" value="US">
        		<input type="hidden" name="custom" value="<?=$webUserID?>">

        		<input type="hidden" name="a3" value="<?=$plan['amount']?>">
        		<input type="hidden" name="p3" value="1">
        		<input type="hidden" name="t3" value="M">
        		<input type="hidden" name="src" value="1">
        		<input type="hidden" name="sra" value="1">
        		<input type="hidden" name="notify_url" value="<?=$returnURL?>/ipn.php">
        		<input type="hidden" name="return" value="<?php echo $returnURL; ?>">
        		<input type="hidden" name="cancel_return" value="<?=$returnURL?>">
        	</form>



            <?php
		}else{
			$message = '<div class="alert alert-danger">Error occured while saving your profile information! please try again.</div>';
            $_SESSION['message'] = $message;
	        header("location: ".$_SERVER['HTTP_REFERER']);
		}
	}else{
		$message = '<div class="alert alert-danger">An account is already exists with the same email, try another.</div>';
        $_SESSION['message'] = $message;
	    header("location: ".$_SERVER['HTTP_REFERER']);
	}
}else{
	$message = '<div class="alert alert-danger">Repeat Password field is not matching with your original password.</div>';
    $_SESSION['message'] = $message;
	header("location: ".$_SERVER['HTTP_REFERER']);
}

function getServerURL()
{

    $protocol = ( ((!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] !== 'off')) || ($_SERVER['SERVER_PORT'] == 443) ) ? "https://" : "http://";
	$serverName = $_SERVER['SERVER_NAME'];
	$filePath = $_SERVER['REQUEST_URI'];
	$withInstall = substr($filePath,0,strrpos($filePath,'/')+1);
	$serverPath = $protocol.$serverName.$withInstall;
	$applicationPath = $serverPath;

//	if(strpos($applicationPath,'http://www.')===false)
//	{
//		if(strpos($applicationPath,'www.')===false)
//			$applicationPath = 'www.'.$applicationPath;
//		if(strpos($applicationPath,'http://')===false)
//			$applicationPath = 'https://'.$applicationPath;
//	}
	return $applicationPath;
}
?>
<script>
	document.forms['paypalForm'].submit();
</script>
