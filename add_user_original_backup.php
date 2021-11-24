<?php

header('Access-Control-Allow-Origin: *');
@session_start();
include_once("core/database.php");
function decode($str){
	return substr($str,2,strlen($str)-12);
}
$id  = decode($_REQUEST['pid']);
$uid = $_REQUEST['uid'];

$db=new Database();
// $sql = "select * from plans where id='".$id."'";
// $res = $db->connection->query($sql);
// if($res->num_rows){
// 	$row = $res->fetch_assoc();
// }

$row=$db->getPlanById($id);
$settings = $db->getGeneralSettings();


?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<link rel="shortcut icon" href="images/favi.png">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">


<title>SMBreviewer Review Tool - Get Started</title>

<!-- <link href="/assets/css/bootstrap.min.css" rel="stylesheet" />
<link href="/assets/css/animate.min.css" rel="stylesheet"/> -->
</head>
<style>
.footer{
	left:0px !important;
	text-align:center !important
}
input[type="checkbox"]{
	opacity:1 !important;
}
.alert-success {
    background-color: #dff0d8;
    border-color: #d0e9c6;
    color: #3c763d;
}
.alert-danger {
    background-color: #f2dede;
    border-color: #ebcccc;
    color: #a94442;
}
.alert {
    border: 1px solid transparent;
    border-radius: 0.25rem;
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
    margin-top: 25px;
}
</style>
<body class="fixed-left">

<!-- Begin page -->
<div id="wrapper">


<div class="content-page" style="margin:35px auto !important">
	<div class="content">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="card-box">
						<div class="row">
							<div class="col-lg-12">
								<h4 class="m-t-0 header-title"><b>Please provide the information below:</b></h4>
	<p class="text-muted font-13">
		You're just one step away.
	</p>
	<?php

        if(isset($_SESSION['message']) && $_SESSION['message']!=""){
            echo $_SESSION['message'];
        }
        unset($_SESSION['message']);

	?>
	<div class="p-20">
		<h4>You're signing up for the <span style="color:#FF8700"><?php echo $row['title']?></span> @ <span style="color:red"><?php echo '$'.$row['amount']?> per month.</span></h4>
		<form method="post" enctype="multipart/form-data" action="/billing.php">
			<div class="form-group">
				<label>Name</label>
				<input type="text" name="name" class="form-control" required>
			</div>
			<div class="form-group">
				<label>Login Email</label>
				<input type="email" name="email" class="form-control" required>
			</div>
            <div class="form-group">
				<label>Phone Number for SMS Notifications (Optional)</label>
				<input type="phone" name="phone" class="form-control">
			</div>
			    <div class="form-group">
				<label>Website (https://www.yourwebsite.com)</label>
				<input type="url" name="address" class="form-control" required>
			</div>
			<div class="form-group">
				<label>Login Password</label>
				<input type="password" name="password" class="form-control" required>
			</div>
			<div class="form-group">
				<label>Re-type Password</label>
				<input type="password" name="retype_password" class="form-control" required>
			</div>


            <div class="form-group">
				<label class="checkbox"><input type="checkbox" name="privacy_policy" value="1" required style="margin-left:0px !important; margin-right:5px !important; position:relative !important">T&C/Privacy Policy <a href="<?php echo $settings['privacy_policy_url']; ?>">Read here</a></label>
			</div>
			<div class="form-group text-right m-b-0">
                <!-- package fields -->
				<input type="hidden" name="pkg_id" value="<?php echo $id?>">
				<input type="hidden" name="pkg_price" value="<?php echo $row['amount']?>">
				<input type="hidden" name="pkg_title" value="<?php echo $row['title']?>">
				<button class="btn btn-primary waves-effect waves-light" type="submit"> Sign up Now </button>
				<button type="reset" class="btn btn-default waves-effect waves-light m-l-5" onclick="window.location = 'javascript:history.go(-1)'"> Cancel </button>
			</div>
		</form>
	</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<footer class="footer">
		Powered by <p class="text-center text-muted mt-md mb-md" >&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbreviewer.com" target="_blank">SMBreviewer</a>.</p>
	</footer>
</div>
</div>

<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script src="https://app.syncspider.com/api/v1/htmlforms/source/1962/form/snippet/smartForm"></script>

</body>
