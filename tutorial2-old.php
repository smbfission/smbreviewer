<?php
include_once("header.php");
include_once("sidebar.php");


if(isset($_REQUEST['business_email']) ){ 
    $sql = "update settings set business_email = '".$_REQUEST['business_email']."' ";
    mysqli_query($conn,$sql);
}

$sql = "select * from settings";
$exe = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($exe);
?>
<section role="main" class="content-body">
					<header class="page-header">
						<h2>Tutorial</h2>
					
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
                    
                    <!-- start: page -->
						<section class="panel">
						    <div class="panel-body">
								<div class="row">
                                    <div class="col-sm-12">
        								<h2 class="h2 mt-none mb-sm text-dark text-bold">
                                            Tutorial
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
                                
                                    
<style>
img{
    -webkit-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
    -moz-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
    box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
}
</style>

<h3> Installation Tutorial </h3>
Click to see <a target="_blank" href="docs/index.html">Installation Tutorial</a> 
<br /><br />                    
            
                                
<h3>Setup Facebook App</h3>
<br>

<h5>
    Before create the Facebook App you need few below mentioned URLs.
</h5>
<h3>
    <br>A)	App Domains : <?php echo $_SERVER['HTTP_HOST']; ?> 
    <span style="color:red; font-size:10px;">Notes : App Domain without http:// or https:// </span>
</h3>
<h3>
    B)	Site URL : https://<?php echo getServerURL(); ?>api.php
</h3>
<br>
<h4>
    <span style="color:red;">Notes : Your application must be SSL Enabled (https://) </span>
</h4>

<h5>Now go to <a target="_blank" href="https://developers.facebook.com/apps/">Facebook Developers</a>  to Create Facebook App.</h5>


Create an app and aave basic settings as described below

<br /><br /><br />
<img src="assets/img/6.png" style="width:100%;">
<br><br><br>
<img src="assets/img/s2.png" style="width:100%;">
<br><br><br>
<img src="assets/img/s3.png" style="width:100%;">
<br><br><br>
<img src="assets/img/s4.png" style="width:100%;">
<br><br><br>
<img src="assets/img/s5.png" style="width:100%;">

<br><br><br /><br />

<h5>After saving basic settings, Click on Add Products and Setup Facebook Login</h5>
<br><br><br>
<img src="assets/img/s6.png" style="width:100%;">
<br><br><br>
<img src="assets/img/s7.png" style="width:100%;">
<br><br><br />

<h5>After the above process, Now make you app live</h5>
<br>
<img src="assets/img/s8.png" style="width:100%;">



<br /><br />
 

<br><br>
Now after complete this process, Enter your App Credentials into your Application as mentioned below.
<br>
<br>
  <h3>
  Connect to Facebook App: 
  </h3>
<br>Enter you Facebook App Credentials &amp; Save to Proceed.
  <br><br><br />
  <img src="assets/img/s9.png" style="width:100%;">
  <br><br><br />
  <img src="assets/img/s10.png" style="width:100%;">
  
 <br>
<br>After enter Facebook App Credentials, There will be "Connect to Facebook" Button. Click on the button and Application will take you to Facebook site to get required permission.
  <br>
<br>Notes: This is a onetime process only.


<h3>Setup Google Project</h3>
<br>

<h5>Now to get "Google Project Key" go to <a target="_blank" href="https://console.cloud.google.com/projectcreate">Google Console</a> to Create Project.</h5>


Create google project and aave basic settings as described below

<br /><br /><br />
<img src="assets/img/g1.png" style="width:100%;">
<br><br><br>
<img src="assets/img/g2.png" style="width:100%;">
<br><br><br>
<img src="assets/img/g3.png" style="width:100%;">
<br><br><br>
<img src="assets/img/g4.png" style="width:100%;">
<br><br><br>
<img src="assets/img/g5.png" style="width:100%;">
<br><br><br>
<img src="assets/img/g6.png" style="width:100%;">
<br><br><br>
<img src="assets/img/g7.png" style="width:100%;">
<br><br><br>
<img src="assets/img/g8.png" style="width:100%;">
<br><br><br>
<img src="assets/img/g9.png" style="width:100%;">
<br><br><br>
<img src="assets/img/g10.png" style="width:100%;">

<br /><br /><br />

<h3>Setup Custom Review (Optional Step) </h3>
<br>

<h5>You can create custom reviews yourself own behalf of your clients/customers</h5>

create custom reviews as described below.  

<br /><br /><br />
<img src="assets/img/cus1.png" style="width:100%;">
<br><br><br>
<img src="assets/img/cus2.png" style="width:100%;">
<br />


<br><br>
<h3>
Setup Campaigns:
</h3> 
<br><br>
We can setup campaigns to manage reviews from your Google/Facebook businesses and cutomise its styling, Get the embed code and post on your website to show designed reviews 
<br>
<br>Here is the detail of get and design reviews

<br><br>

<img src="assets/img/c1.png" style="width:100%;">
<br /><br /><br />
<img src="assets/img/c2.png" style="width:100%;">
<br /><br /><br />
<img src="assets/img/c3.png" style="width:100%;">
<br /><br /><br />
<img src="assets/img/c3b.png" style="width:100%;">
<br /><br /><br />
<img src="assets/img/c4.png" style="width:100%;">
<br /><br /><br />
<img src="assets/img/c5.png" style="width:100%;">
<br /><br /><br />
<img src="assets/img/c6.png" style="width:100%;">
<br /><br /><br />
<img src="assets/img/c7.png" style="width:100%;">
<br /><br /><br />
<img src="assets/img/c8.png" style="width:100%;">
<br /><br /><br />
<img src="assets/img/c9.png" style="width:100%;">
<br /><br />
<br>


                                
                            
                                </div>
                            
        <!-- END wrapper -->


        <?php
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
            $applicationPath = str_replace(array("www.","http://","https://"),"",$applicationPath);
            return $applicationPath;
        }
        ?>
 </section>            
					<!-- end: page -->
				</section>
			</div>
            
		</section>

		<!-- Vendor -->
		<script src="assets/vendor/jquery/jquery.js"></script>
		<script src="assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="assets/vendor/nanoscroller/nanoscroller.js"></script>
		<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>
		<script src="assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
		
		<!-- Specific Page Vendor -->
		<script src="assets/vendor/select2/select2.js"></script>
		<script src="assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
		<script src="assets/vendor/jquery-datatables/extras/TableTools/js/dataTables.tableTools.min.js"></script>
		<script src="assets/vendor/jquery-datatables-bs3/assets/js/datatables.js"></script>
		
		<!-- Theme Base, Components and Settings -->
		<script src="assets/javascripts/theme.js"></script>
		
		<!-- Theme Custom -->
		<script src="assets/javascripts/theme.custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="assets/javascripts/theme.init.js"></script>


		<!-- Examples -->
		<script src="assets/javascripts/tables/examples.datatables.default.js"></script>
		<script src="assets/javascripts/tables/examples.datatables.row.with.details.js"></script>
		<script src="assets/javascripts/tables/examples.datatables.tabletools.js"></script>
        
        <script>
        
        function deleteMe(url){
            if(confirm("Are you sure?")){
                window.location = url;
            }
            return false;
        }
        
        </script> 
        
        
	</body>
</html>