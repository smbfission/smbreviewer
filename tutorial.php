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

<h3> SMBreviewer Setup Tutorial </h3>
View the videos below to get an understanding as to how to utilize the reviews, if you have any questions, you can send your inquiries to our support channel.
<br /><br />





                                </div>
                                   <div style="text-align:center;"> <h3> SMBreviewer Full Setup Tutorial (All Videos) </h3></div>
                                <div style="bgcolor:#ffffff;height: 100%; display: flex; align-items: center; justify-content: center;">
                                 
                                       <div class="guidez3rdpjs-modal" data-key="fgb1bs0t0r-pvvm6g1e8b" data-mtype="c">
                                           <img width="600" src="https://s3-eu-west-1.amazonaws.com/guidez-thumbnails/p/fgb1bs0t0r-pvvm6g1e8b_600.jpg?t=1585489925"></div><script src="https://sdk.fleeq.io/fleeq-sdk-light.js"></script> 
                                  
                                </div>
<div style="text-align:center;margin-top:50px"> <h3> SMBreviewer Facebook Connection </h3></div>
<div style="bgcolor:#ffffff;height: 100%; display: flex; align-items: center; justify-content: center;">
<div class="guidez3rdpjs-modal" data-key="rb4elsa93r-x3gb8g98xr" data-mtype="g"><img alt="Thumbnail" width="600" src="https://s3-eu-west-1.amazonaws.com/guidez-thumbnails/p/rb4elsa93r-x3gb8g98xr_600.jpg"></div><script src="https://sdk.fleeq.io/fleeq-sdk-light.js"></script>
</div>
<div style="text-align:center;margin-top:50px"> <h3> SMBreviewer Google Connection </h3></div>
<div style="bgcolor:#ffffff;height: 100%; display: flex; align-items: center; justify-content: center;">
    <div class="guidez3rdpjs-modal" data-key="4jriji2eyg-t7icnuj9dn" data-mtype="g"><img alt="Thumbnail" width="600" src="https://s3-eu-west-1.amazonaws.com/guidez-thumbnails/p/4jriji2eyg-t7icnuj9dn_600.jpg"></div><script src="https://sdk.fleeq.io/fleeq-sdk-light.js"></script>
    </div>
<div style="text-align:center;margin-top:50px"> <h3> SMBreviewer Yelp Connection </h3></div>
<div style="bgcolor:#ffffff;height: 100%; display: flex; align-items: center; justify-content: center;">
    <div class="guidez3rdpjs-modal" data-key="jfwvkzgydg-nd1nwo0eca" data-mtype="g"><img alt="Thumbnail" width="600" src="https://s3-eu-west-1.amazonaws.com/guidez-thumbnails/p/jfwvkzgydg-nd1nwo0eca_600.jpg"></div><script src="https://sdk.fleeq.io/fleeq-sdk-light.js"></script>
    </div>
    <div style="text-align:center;margin-top:50px"> <h3> SMBreviewer Custom Reviews </h3></div>
<div style="bgcolor:#ffffff;height: 100%; display: flex; align-items: center; justify-content: center;">
    <div class="guidez3rdpjs-modal" data-key="309eh6w70e-7niup0buap" data-mtype="g"><img alt="Thumbnail" width="600" src="https://s3-eu-west-1.amazonaws.com/guidez-thumbnails/p/309eh6w70e-7niup0buap_600.jpg"></div><script src="https://sdk.fleeq.io/fleeq-sdk-light.js"></script>
    </div>
     <div style="text-align:center;margin-top:50px"> <h3> SMBreviewer Bulk Reviews Importing </h3></div>
<div style="bgcolor:#ffffff;height: 100%; display: flex; align-items: center; justify-content: center;">
    <div class="guidez3rdpjs-modal" data-key="ynosrxw4q3-lqb2hbkha2" data-mtype="g"><img alt="Thumbnail" width="600" src="https://s3-eu-west-1.amazonaws.com/guidez-thumbnails/p/ynosrxw4q3-lqb2hbkha2_600.jpg"></div><script src="https://sdk.fleeq.io/fleeq-sdk-light.js"></script>
    </div>
    <div style="text-align:center;margin-top:50px"> <h3> SMBreviewer Campaigns </h3></div>
<div style="bgcolor:#ffffff;height: 100%; display: flex; align-items: center; justify-content: center;">
    <div class="guidez3rdpjs-modal" data-key="jrjghj0m1i-2qe3o1wt7n" data-mtype="g"><img alt="Thumbnail" width="600" src="https://s3-eu-west-1.amazonaws.com/guidez-thumbnails/p/jrjghj0m1i-2qe3o1wt7n_600.jpg"></div><script src="https://sdk.fleeq.io/fleeq-sdk-light.js"></script>
    </div>
  

    <div style="text-align:center;margin-top:50px"> <h3> SMBreviewer Profile </h3></div>
<div style="bgcolor:#ffffff;height: 100%; display: flex; align-items: center; justify-content: center;">
    <div class="guidez3rdpjs-modal" data-key="uxhjaqrqb3-bsy5tmv30v" data-mtype="g"><img alt="Thumbnail" width="600" src="https://s3-eu-west-1.amazonaws.com/guidez-thumbnails/p/uxhjaqrqb3-bsy5tmv30v_600.jpg"></div><script src="https://sdk.fleeq.io/fleeq-sdk-light.js"></script>
    </div>
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
    
<?php include_once('footer_default.php'); ?>

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
