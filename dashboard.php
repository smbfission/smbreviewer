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
						<h2>Your Dashboard</h2>

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
                            
                                
                                
                                <div>
                                
  <!-- Code here  -->

	<div class="container-fluid">
    <div class="row d-md-flex flex-wrap">
      <div class="col-lg-3 col-md-6 p-2">
        <div class="card bg-dark text-light h-100">
          <div class="card-body">
            <h4 class="text-center" style="font-size:20px !important;">Ring the <i id="changelog" class="fa fa-bell" ></i> Above to See Updates</h4>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 p-2">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <h1 class="mr-3">
                <i class="bi bi bi-bar-chart"></i>
              </h1>
              <div>
                <h4 class="card-title"  style="font-weight:bold !important;"> <?php
$exe = mysqli_query($con,"SELECT * FROM custom_reviews WHERE `user_id` = '".$_SESSION['user_id']."' AND DATE(date) >= DATE(NOW()) - INTERVAL 30 DAY");
echo $exe->num_rows;

 ?></h4>
                <p class="card-subtitle">New Reviews in the Last 30 Days</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 p-2">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <h1 class="mr-3" style="font-weight:bold;">
                <i class="bi bi-person-square""></i>
              </h1>
              <div>
                <h4 class="card-title"  style="font-weight:bold !important;"><?php
$exe = mysqli_query($con,"SELECT * FROM custom_reviews WHERE `user_id` = '".$_SESSION['user_id']."'");
echo $exe->num_rows;

 ?></h4>
                <p class="card-subtitle">Total Reviews Captured</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 p-2">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <h1 class="mr-3">
                <i class="bi bi-people"></i>
              </h1>
              <div>
                <h4 class="card-title" style="font-weight:bold !important;">N/A<?php echo @$_SESSION['visit_qty']; ?> <?php echo $current_user['visit_qty'] ?></h4>
                <p class="card-subtitle">Campaign Views in the Last 30 Days</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6 col-md-4 col-sm-6 p-2">
        <div class="card h-100" style="min-height:360px;">
          <div class="card-body" >
            <h4 class="card-title">How to Get Started</h4>
            <div class="pl-3" style="margin-bottom:10px;">
             See How to Create Review Capture Campaign</div>
             <div>
            <iframe class="w-100" height="100%" src="https://www.youtube.com/embed/559lECQYe_8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div><div style="margin:auto ;width:50%;"><a href="https://reviews.smbreviewer.com/capture_reviews_add/"><div class="btn btn-warning btn-lg mt-4" style="background-color:#ff761d !important;">Create a New Campaign</div></div></a>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 p-2">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">What We're Working On</h4>
            <div  style="min-height:300px;" class="pl-3">
             <ul>
              <li> Video Embed Campaigns (Jan 2022)</li>
              
             <li> Wordpress Plugin (Jan 2022)</li>
             <li> Android/iOS apps (Q4 2021)</li>
             <li> More Templates (Jan 2022)</li>
             <li> Official Google Business Profile Integration (Jan 2022)</li>
               <li> Review Content Maps (Q1 2022)</li>
               <li> Social Proof Views (Q1 2022)</li>
             </ul>
             </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-4 col-sm-6 p-2">
        <div class="card h-100">
          <div class="card-body">
            <h4 class="card-title">How To:</h4>
            <div class="accordion mt-4" id="accordionExample">
              <div class="card h-100">
                <div class="card-header" id="headingOne">
                  <h2 class="my-0">
                    <button class="btn" style="white-space:normal" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                     Create a Review Capture Campaign
                    </button>
                  </h2>
                </div>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                  <div class="card-body">
        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/559lECQYe_8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                  </div>
                </div>
              </div>
              <div class="card h-100">
                <div class="card-header" id="headingTwo">
                  <h2 class="my-0">
                    <button class="btn" type="button" style="white-space:normal" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                      Connect Your Facebook Account
                    </button>
                  </h2>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                  <div class="card-body">
                   <iframe width="100%" height="100%" src="https://www.youtube.com/embed/KMm2hZS46-Y" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-header" id="headingThree">
                  <h2 class="my-0">
                    <button class="btn" type="button" style="white-space:normal" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                     Create a Display Campaign
                    </button>
                  </h2>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                  <div class="card-body">
           <iframe width="100%" height="100%" src="https://www.youtube.com/embed/nG3ZFr0tU74" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    
    
     <div class="row">
      <div class="col-lg-6 col-md-4 col-sm-6 p-2">
        <div class="card h-100" style="min-height:360px;">
          <div class="card-body" >
            <h4 class="card-title">How to Get Started</h4>
            <div class="pl-3" style="margin-bottom:10px;">
             See How to Create Review Capture Campaign</div>
             <div>
            <iframe class="w-100" height="100%" src="https://www.youtube.com/embed/559lECQYe_8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div><div style="margin:auto ;width:50%;"><a href="https://reviews.smbreviewer.com/capture_reviews_add/"><div class="btn btn-warning btn-lg mt-4" style="background-color:#ff761d !important;">Create a New Campaign</div></div></a>
          </div>
        </div>
      </div>
    
    
    
    
    <div class="row">
      <div class="col-lg-3 col-md-4 col-sm-6 p-2">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Important Stats</h4>
            <div class="mt-4">
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <h2 class="mr-3">
                   <i class="bi bi-star"></i>
                  </h2>
                  <h5 class="text-dark">Number of People Only Do Business With 4 or 5 Stars?</h5>
                </div>
                <h4 class="font-weight-bold ml-4" style="font-weight:bold;">45%<a target="blank" href="https://www.brightlocal.com/research/local-consumer-review-survey/"><sup>1</sup></a></h4>
              </div>
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <h2 class="mr-3">
                    <i class="bi bi-pc-display-horizontal"></i>
                  </h2>
                  <h5 class="text-dark">Percent of People Who Used Online Reviews to Find a Local Business Daily or Weekly</h5>
                </div>
                <h4 class="font-weight-bold ml-4" style="font-weight:bold;">48%<a target="blank" href="https://my.sendinblue.com/users/subscribe/js_id/2zcpr/id/7"><sup>2</sup></a></h4>
              </div>
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <h2 class="mr-3">
                 <i class="bi bi-cart-check-fill"></i>
                  </h2>
                  <h5 class="text-dark">Percent of consumers who need to read at least 4 reviews before purchasing</h5>
                </div>
                <h4 class="font-weight-bold ml-4" style="font-weight:bold;">54.7%<a target="blank" href="https://www.bizrateinsights.com/resources/shopper-survey-report-the-impact-reviews-have-on-consumers-purchase-decisions/"><sup>3</sup></a></h4>
              </div>
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <h2 class="mr-3">
                   <i class="bi bi-google"></i>
                  </h2>
                  <h5 class="text-dark">Percent of People who Read Their Reviews on Google Business Profile, Yelp, or Facebook Local</h5>
                </div>
                <h4 class="font-weight-bold ml-4" style="font-weight:bold;">72%<a target="blank" href="https://smbreviewer.com/local-reviews-data/"><sup>4</sup></a></h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>




      <!-- End Your Code here  -->
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
