<?php
ob_start();
include_once("header.php");
include_once("sidebar.php");


if ($_SESSION['type']!=1) {
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: /campaign/");
  exit();
}

$db = new Database();


?>
 <section role="main" class="content-body">
					<header class="page-header">
						<h2>Payments</h2>

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
                                            Payment History
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



                                    <table id="datatable-responsive1"
                                           class="table table-striped  table-colored table-info dt-responsive nowrap" cellspacing="0"
                                           width="100%" style="font-size:12px;">
                                        <thead>
                                            <tr>
                                                <th>Item Name</th>
                                                <th>Amount </th>
                                                <th>Payment Status</th>
                                                <th>Client</th>
                                                <th>Payment Date</th>
                                                <th>Payer Email</th>
                                                <th>Txn ID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php




                                            $filter = "";
                                            if(isset($_REQUEST['id']) && $_REQUEST['id']!="" && $_REQUEST['id']!="0"){
                                                $filter = " and page_id = '".$_REQUEST['id']."'";
                                            }

                                            $columns = "id,item_name,payment_gross,payment_status,payment_date,payer_email,txn_id,created_user";
        									$sql = "SELECT $columns FROM `client_payments` where payment_status != '' $filter order by payment_date desc";
        									$result = $conn->query($sql);
                                            $showingRec = $result->num_rows;
        										if($showingRec > 0){
        											while($row = $result->fetch_assoc()) {

                                                    $created_user  = @$db->getUserProfile($row['created_user'])['name'];
                                                    if($row['payment_status']=="comment_reply"){
                                                        $label = "comment-o"; $class = "success";
                                                    }
                                                    else
                                                    if($row['payment_status']=="personal_msg"){
                                                        $label = "envelope-o"; $class = "primary";
                                                    }
                                                    else{
                                                        $label = "thumbs-o-up"; $class = "info";
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $row['item_name']; ?></td>
                                                        <td><?php echo $row['payment_gross']; ?></td>
                                                        <td><?php echo $row['payment_status']; ?></td>
                                                        <td><?php echo $created_user; ?></td>
                                                        <td><?php echo $row['payment_date']; ?></td>
                                                        <td><?php echo $row['payer_email']; ?></td>
                                                        <td><?php echo $row['txn_id']; ?></td>
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
