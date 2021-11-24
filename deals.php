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

<style>
img{
    -webkit-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
    -moz-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
    box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
}

</style>
<section role="main" class="content-body">
					<header class="page-header">
						<h2>Lifetime Software Deals</h2>

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
                                         Lifetime Deals
                            </h2>


                      <iframe id="iframe" width="100%" onload="this.style.height=this.contentDocument.body.scrollHeight +'px';" style="border: none;"></iframe>

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
              </div>

        <!-- END wrapper -->

          </section>
					<!-- end: page -->
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

        $(document).ready(function() {


        $.ajax({
            url: 'https://b3ware.fyi.to/lifetime-deals',
            type: 'GET',
            success: function(res) {

           var doc = document.getElementById('iframe').contentWindow.document;
           doc.open();
           var s = res.indexOf("<nav>"),
             e = res.indexOf("</nav>")+6;

            res=res.substring(0, s)+res.substring(e,res.length);


           doc.write(res);
           doc.close();
           setTimeout(function(){$('#iframe').css('height',document.getElementById('iframe').contentDocument.body.scrollHeight);}, 100);

            }
        });

        });

        </script>


	</body>
</html>
