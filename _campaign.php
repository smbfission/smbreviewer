<?php
include_once("header.php");
include_once("sidebar.php");
?>

 <!-- DataTables -->
<link href="../plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
<link href="../plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../plugins/datatables/dataTables.colVis.css" rel="stylesheet" type="text/css"/>
<link href="../plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../plugins/datatables/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>


            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                        <div class="row">
							<div class="col-xs-12">
								<div class="page-title-box">
                                    <h4 class="page-title">Campaigns</h4>
                                    <!--
                                    <ol class="breadcrumb p-0 m-0">
                                         <li>
                                            <a href="dashboard.php">Dashboard</a>
                                        </li>
                                        <li class="active">
                                            Campaigns
                                        </li>
                                    </ol>
                                    -->
                                    <div class="clearfix"></div>
                                </div>
							</div>
						</div>
                        
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                
                                    <?php
                                    if(isset($_SESSION['msg']) && $_SESSION['msg']!=""){
                                        ?>
                                        <div class="alert alert-info"><?php echo $_SESSION['msg']; ?></div>
                                        <?php
                                        unset($_SESSION['msg']);
                                    }
                                    ?>

                                    <h4 class="m-t-0 header-title"><b>Review Display Campaigns</b></h4>
                                    <p class="text-muted font-13 m-b-15">
                                        <!-- Description Goes Here <br /> -->  
                                        
                                        <?php
                                        $sql3 = "SELECT no_of_campaigns from user where id = '".$_SESSION['user_id']."'";
    									$result3 = mysqli_query($conn,$sql3);
                                        $records3 = mysqli_fetch_assoc($result3);
                                        
                                        //print_r($records3);
                                        
                                        $no_of_campaigns = $records3['no_of_campaigns'];
                                        
                                        
    									$sql2 = "SELECT id from campaigns where user_id = '".$_SESSION['user_id']."'";
    									$result2 = mysqli_query($conn,$sql2);
                                        $records2 =  mysqli_num_rows($result2);
                                        
    									if ($records2 <= $no_of_campaigns) {
    									   if($no_of_campaigns>"0")
                                           {
    									   ?>
    									 	<a style="margin-top:30px;" class="btn btn-fill btn-primary m-t-20" data-toggle="modal" data-target="#con-close-modal">Add New Campaign</a>
                                            <?php
    									   }
                                        }
                                        ?>
                                    </p>
                                    
                                    
                                    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                        <form action="action.php?action=save_campaigns" method="post">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title">Create a New Display Campaign</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="title" class="control-label">Campain Name</label>
                                                                <input type="text" class="form-control" id="title" name="title">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    
                                                    
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-info waves-effect waves-light">Save and Next</button>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div><!-- /.modal -->
                                    

                                    <table id="datatable-responsive"
                                           class="table table-striped  table-colored table-info dt-responsive nowrap" cellspacing="0"
                                           width="100%" style="font-size:12px;">
                                        <thead>
                                            <tr>
                                                <th>Campaign</th>
                                                <th>Title</th>
                                            	<th>Facebook Business/Page</th>
                                            	<th>Google Business/Place</th>
        										<th>Created Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
        									$sql = "SELECT * from campaigns where user_id = '".$_SESSION['user_id']."'";
    										$result = $conn->query($sql);
                                            $records = $result->num_rows;
    
    										if ($records > 0) {
    										  $sr = 1;
    											while($row = $result->fetch_assoc()) {
    											?>
													
                                        <tr>
                                        	<td><?php echo $sr++;?></td>
                                        	<td><?php echo $row['title'];?></td>
                                        	<td><?php echo $row['page_name']; ?></td>
                                            <td><?php echo $row['google_business']; ?></td>
                                            <td><?php echo $row['created_date']; ?></td>
                                            <td>	
                                                <!--- Start of Action Coloum-->
    											<div class="hidden-sm hidden-xs btn-group">
    												<a href="campaign_add.php?id=<?php echo $row['id']; ?>" class="btn btn-fill btn-xs btn-info">
    													<i class="ace-icon fa fa-pencil bigger-120"></i>
    												</a>
                                                    
    												<a onclick="return deleteMe('action.php?id=<?php echo $row['id']; ?>&action=delete_camp')" class="btn btn-fill btn-xs btn-danger">
    													<i class="ace-icon fa fa-trash-o bigger-120"></i>
    												</a>
    											</div>
                                                <!--- End of Action Colum-->
    										</td>
                                        
											
											
											
											
                                        </tr>
<?php
											}
										} else {
											//echo "No Records Found";
										}
						?>	
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div> <!-- container -->

                </div> <!-- content -->

                <?php include_once("footer.php"); ?>

            </div>

        </div>
        <!-- END wrapper -->


        <script>
            var resizefunc = [];
        </script>

        <!-- jQuery  -->
        <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/waves.js"></script>
        <script src="assets/js/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>
        <script src="../plugins/switchery/switchery.min.js"></script>

        <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="../plugins/datatables/dataTables.bootstrap.js"></script>

        <script src="../plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="../plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="../plugins/datatables/jszip.min.js"></script>
        <script src="../plugins/datatables/pdfmake.min.js"></script>
        <script src="../plugins/datatables/vfs_fonts.js"></script>
        <script src="../plugins/datatables/buttons.html5.min.js"></script>
        <script src="../plugins/datatables/buttons.print.min.js"></script>
        <script src="../plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="../plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="../plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="../plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="../plugins/datatables/dataTables.scroller.min.js"></script>
        <script src="../plugins/datatables/dataTables.colVis.js"></script>
        <script src="../plugins/datatables/dataTables.fixedColumns.min.js"></script>
        
        <!-- init -->
        <script src="assets/pages/jquery.datatables.init.js"></script>


<script>

function deleteMe(url){
    if(confirm("Are you sure?")){
        window.location = url;
    }
    return false;
}

</script>  

        <!-- App js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>
        
        <script type="text/javascript">
            $(document).ready(function () {
                $('#datatable-responsive').DataTable();
            });
            TableManageButtons.init();

        </script>

    </body>
</html>