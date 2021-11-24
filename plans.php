<?php
ob_start();
include_once("header.php");
include_once("sidebar.php");

if ($_SESSION['type']!=1) {
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: /campaign/");
  exit();
}
?>

 <section role="main" class="content-body">
					<header class="page-header">
						<h2>Plans</h2>

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
                                            Plans
                                            <a style="margin-bottom:5px;" class="btn btn-fill btn-primary pull-right" type="submit" href="/plans_add/">Add New Plan</a>
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


                                    <table id="datatable-responsive"
                                           class="table table-striped  table-colored table-info dt-responsive nowrap" cellspacing="0"
                                           width="100%" style="font-size:12px;">
                                        <thead>
                                            <tr>
                                                <th>SR</th>
												<th>Title</th>
                                            	<th>Amount</th>
                                                <th>No of Campaigns</th>
                                            	<th>Description</th>
        										<th>Status</th>
        										<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i=1;
        									$sql = "SELECT * from plans";
        									$exe = mysqli_query($conn,$sql);

        										if (mysqli_num_rows($exe) > 0) {
        											// output data of each row
        											while($row = mysqli_fetch_assoc($exe)) {
        											?>

                                                <tr>
                                                	<td><?php echo $i++; ?></td>
                                                	<td><?php echo $row['title'];?></td>
                                                	<td><?php echo $row['amount']; ?> </td>
                                                    <td><?php echo $row['no_of_campaigns']; ?> </td>
                                                    <td><?php echo $row['description']; ?> </td>
                                                    <td>
                                                        <?php if($row['active'] == "1"){ $class = "success"; $label="Active"; }else{ $class = "danger"; $label="Disabled"; } ?>
                                                        <span class="label label-<?php echo $class; ?>"><?php echo $label; ?></span>
                                                    </td>
                              											<td>
                              												<div class="hidden-sm hidden-xs btn-group " style="display: inline-flex;" >
                              													<a href="/plans_add?id=<?php echo $row['id']; ?>" class="btn btn-fill btn-xs btn-info">
                              														<i class="ace-icon fa fa-pencil bigger-120"></i>
                              													</a>

                              													<a onclick="return deleteMe('/action.php?id=<?php echo $row["id"]; ?>&action=delete_plan')" class="btn btn-fill btn-xs btn-danger">
                              														<i class="ace-icon fa fa-trash-o bigger-120"></i>
                              													</a>
                              												</div>
                              											</td>
                                                </tr>
        <?php
        											}
        										}
        						?>
                                        </tbody>
                                    </table>




                    </div> <!-- container -->


                    </section>
					<!-- end: page -->
				</section>
			</div>

		</section>

		<!-- Vendor -->
		<script src="/assets/vendor/jquery/jquery.js"></script>
		<script src="/assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="/assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="/assets/vendor/nanoscroller/nanoscroller.js"></script>
		<script src="/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="/assets/vendor/magnific-popup/magnific-popup.js"></script>
		<script src="/assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>

		<!-- Specific Page Vendor -->
		<script src="/assets/vendor/select2/select2.js"></script>
		<script src="/assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
		<script src="/assets/vendor/jquery-datatables/extras/TableTools/js/dataTables.tableTools.min.js"></script>
		<script src="/assets/vendor/jquery-datatables-bs3/assets/js/datatables.js"></script>

		<!-- Theme Base, Components and Settings -->
		<script src="/assets/javascripts/theme.js"></script>

		<!-- Theme Custom -->
		<script src="/assets/javascripts/theme.custom.js"></script>

		<!-- Theme Initialization Files -->
		<script src="/assets/javascripts/theme.init.js"></script>


		<!-- Examples -->
		<script src="/assets/javascripts/tables/examples.datatables.default.js"></script>
		<script src="/assets/javascripts/tables/examples.datatables.row.with.details.js"></script>
		<script src="/assets/javascripts/tables/examples.datatables.tabletools.js"></script>

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
