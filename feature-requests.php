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
                                           Feature Requests
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

<h3>What Would You Like to See in Future Developments?</h3>
Use the tool below to list and vote on future features for development. You can also vote on the development features that you like; the more votes, the more likely we'll invest in investing in that idea.
<br /><br />





                                </div>
                                <div>
                                
                                <style>/* RESPONSIVE TREEFORT */
.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; min-height: 700px } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='https://eu-us.productflare.com/r/1573993169195x308785537835335700?topics=' style='border:0'></iframe></div></div>
                  <script>
  const changeUser = (f,l,e) => {
    document.getElementById('treefort-embed').src = "https://eu-us.productflare.com/r/1573993169195x308785537835335700?topics="+f+"&lastName="+l+"&email="+e+"&topics="
  }
</script>
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
