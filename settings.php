<?php
include_once("header.php");
include_once("sidebar.php");

// $sql = "SELECT
// *
// from `user` where `id` = '".$_SESSION['user_id']."'";
//
// $exe = mysqli_query($conn,$sql);
// $row = mysqli_fetch_assoc($exe);

$row = $db->getUserProfile($current_user['id']);


if (trim(@$current_user['app_id'])=="" || trim(@$current_user['app_secret'])=="") {
$access_token = "";

} else {
  $access_token = @$row['access_token'];


}



$row['use_default_credentials'] = 1;

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
    if (isset($_SERVER["HTTPS"])) {
        $protocol = "https://";
    } else {
        $protocol = "http://";
    }

    return $protocol.$serverName.'/';

    //return $applicationPath;


}

$url_me="https://graph.facebook.com/me?access_token=".$access_token;

 // print_r($url_me);


$me=json_decode(post_fb($url_me,"get"),true);


$redirect_url = getServerURL().'api.php';

$redirect_url = "https://".str_replace(array("http://","https://","www."),"",$redirect_url);
$app_id = @$current_user['app_id'];

// $login_url="https://www.facebook.com/dialog/oauth?client_id=$app_id&redirect_uri=$redirect_url&response_type=code&scope=email,public_profile,manage_pages,business_management,pages_show_list";
$login_url="https://www.facebook.com/dialog/oauth?client_id=$app_id&redirect_uri=$redirect_url&response_type=code&scope=email,public_profile,pages_show_list,read_insights,pages_read_engagement,pages_read_user_content";
//$login_url="https://www.facebook.com/dialog/oauth?client_id=$app_id&redirect_uri=$redirect_url&response_type=code&scope=email,public_profile";


if ($current_user['id']==169) {
//  $app_id="481675452476366";
//  $login_url="https://www.facebook.com/dialog/oauth?client_id=$app_id&redirect_uri=$redirect_url&response_type=code&scope=email,public_profile,pages_show_list,pages_read_engagement";

}


?>
        <section role="main" class="content-body">
					<header class="page-header">
						<h2>Facebook Settings</h2>

						<div class="right-wrapper pull-right" style="display:none;">
							<ol class="breadcrumbs">
								<li>
									<a href="index.html">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Tables</span></li>
								<li><span>Advanced</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

                    <div class="row">
						<div class="col-lg-12">
							<section class="panel">
								<div class="panel-body">

                                    <div class="row">
                                        <div class="col-sm-12">
            								<h2 class="h2 mt-none mb-sm text-dark text-bold">
                                                Facebook App Credentials
                                            </h2>
            								<p class="text-muted font-13 m-b-15">
                                                <!-- Description Goes Here <br /> -->
                                                &nbsp;
                                            </p>
            							               </div>

                                        <?php
                                        if(isset($_SESSION['msg']) && $_SESSION['msg']!=""){
                                            ?>
                                            <div class="col-sm-12">
                                                <div class="alert alert-info"><?php echo $_SESSION['msg']; ?></div>
                                            </div>
                                            <?php
                                            unset($_SESSION['msg']);
                                        }
                                        ?>
                                    </div>
                                    <form action="/api.php" method="post" class="form-horizontal form-bordered" >

<?php /* temprary add false cuz fb api limit is big */ if ((int)$row['advanced_settings']==1 && false): ?>

                      <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault">App ID:</label>
											<div class="col-md-6">
                            <input class="form-control" name="app_id" id="app_id" value="<?= $row['app_id'] ?>" type="text"  placeholder="<?= ((@$row['use_default_credentials']==1) ? "default value" : "enter value") ?>"/>
											</div>
	  									</div>

                      <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault">App Secret:</label>
											<div class="col-md-6">
                            <input class="form-control" name="app_secret" id="app_secret" value="<?= $row['app_secret'] ?>" type="text"  placeholder="<?= ((@$row['use_default_credentials']==1) ? "default value" : "enter value") ?>"/>
											</div>
										</div>

    									<div class="row">
    									    <div class="col-md-3" ></div>
    										<div class="col-md-9">
    											<button type="submit" name="submit" value="submit"  class="btn  btn-fill btn-success "><span class="ace-icon fa fa-save bigger-120"></span> Save</button>
                          <button type="submit" name="reset" value="reset"  class="btn  btn-fill btn-danger "><span class="ace-icon fa fa-refresh bigger-120"></span> Reset</button>

                        <?php if (isset($me['id'])): ?>
                            <button type="submit" name="disconnect" value="<?= $me['id']?>"  class="btn  btn-fill btn-primary "><span class="ace-icon fa fa-times bigger-120"></span> Disconnect from <?= $me['name']?></button>
                        <?php elseif ($current_user['app_id']!=""): ?>
                            <a class="btn  btn-fill btn-primary " href="<?= $login_url ?>"><span class="ace-icon fa fa-plug bigger-120"></span> Connect </a>
                        <?php endif; ?>
    										</div>
    									</div>

<?php else: ?>

                      <?php if (isset($me['id'])): ?>
                        <div class="row">
                          <div class="col-md-3" ></div>

                          <div class=" col-md-7">
                            Facebook is connected to <?= $me['name']?>&nbsp;&nbsp;&nbsp;
                            <button type="submit" name="disconnect" value="<?= $me['id']?>"  class="btn  btn-fill btn-danger "><span class="ace-icon fa fa-times bigger-120"></span> Disconnect</button>

                          </div>
                          <div class="col-md-2" ></div>

                        </div>

                        <?php else: ?>

                       <div class="row">
     									    <div class="col-md-3" ></div>

                    		<div class="col-md-9">

                          <a class="btn  btn-fill btn-success " href="<?= $login_url ?>"><span class="ace-icon fa fa-plug bigger-120"></span> Connect </a>

     										</div>
     									</div>
                      <?php endif; ?>


<?php endif; ?>



                                    <div class="row">
                                        <div class="col-sm-3"></div>
                                        <div class="col-sm-9">
                                            <br />
                                            <h5>Need Help? <a href="/tutorial/" target="_blank">Click</a> to see tutorials that will help you to create Facebook App</h5>
                                        </div>
                                    </div>
                                    </div>
							</section>
                        </div>
                    </div>
                    <!-- end: page -->
				</section>
			</div>

            </section>

		<!-- Vendor -->
		<script src="/assets//vendor/jquery/jquery.js"></script>
		<script src="/assets//vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="/assets//vendor/bootstrap/js/bootstrap.js"></script>
		<script src="/assets//vendor/nanoscroller/nanoscroller.js"></script>
		<script src="/assets//vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="/assets//vendor/magnific-popup/magnific-popup.js"></script>
		<script src="/assets//vendor/jquery-placeholder/jquery.placeholder.js"></script>

		<!-- Specific Page Vendor -->
		<script src="/assets//vendor/jquery-autosize/jquery.autosize.js"></script>
		<script src="/assets//vendor/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>

		<!-- Theme Base, Components and Settings -->
		<script src="/assets//javascripts/theme.js"></script>

		<!-- Theme Custom -->
		<script src="/assets//javascripts/theme.custom.js"></script>

		<!-- Theme Initialization Files -->
		<script src="/assets//javascripts/theme.init.js"></script>

	</body>
</html>
