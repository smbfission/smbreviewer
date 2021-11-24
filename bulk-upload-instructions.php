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
						<h2>Bulk Upload Instructions</h2>

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
                                          Custom Review Bulk Import
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

<h3> SMBreviewer Bulk Review Upload Tutorial </h3>
Have a number of reviews but don't want to add your reviews one-by-one? You can easily make an excel file of all of your reviews, save that file as a .csv (comma delimited file), and then upload that file to auto-populate your custom reviews within your account.
<br /><br />





                                </div>
                                   <div style="text-align:center;"> <h3> Bulk Upload Instructions: </h3></div>
                                <div style="bgcolor:#ffffff;height: 100%; display: flex; align-items: center; justify-content: center;">
                                 
                                       <div class="guidez3rdpjs-modal" data-key="ynosrxw4q3-lqb2hbkha2" data-mtype="g">
                                           <img width="600" src="https://s3-eu-west-1.amazonaws.com/guidez-thumbnails/p/ynosrxw4q3-lqb2hbkha2_600.jpg"></div><script src="https://sdk.fleeq.io/fleeq-sdk-light.js"></script> 
                                  
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
