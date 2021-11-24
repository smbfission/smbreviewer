<?php
include_once("header.php");
include_once("sidebar.php");

?>

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Display Reviews</h2>


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
                                            Display Review Campaigns
                                            
																						<?php if ((int)$db->getUserCampaignsCount($_SESSION['user_id']) < (int)$current_user['no_of_campaigns']): ?>
																							<a class="btn btn-fill btn-primary pull-right m-t-20" data-toggle="modal" data-target="#con-close-modal">Add New Campaign</a>
																						<?php endif; ?>

                                            <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                <form action="/action.php?action=save_campaigns" method="post">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                            <h4 class="modal-title">Create a new campaign</h4>
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
                                        </h2>
        								<p class="text-muted font-13 m-b-15">
                                            <!-- Description Goes Here <br /> -->
                                            Here you can create unique and various to play your reviews in various styles, tactics, and other methods that you like. 
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

                                <!-- <table class="table table-bordered table-striped mb-none" id="datatable-default"> -->
																			<table id="datatable-responsive"
                                           class="table  display table-striped table-colored table-info nowrap" cellspacing="0"
                                           style="font-size:12px;width:100%">
                                        <thead>
                                            <tr>
                                              <th data-priority="1" style="vertical-align:middle;">Campaign No.</th>
                                              <th style="vertical-align:middle;">Title</th>
                                            	<th style="vertical-align:middle;white-space: normal;">Facebook Reviews</th>
                                            	<th style="vertical-align:middle;">Google Reviews</th>
                                              <th style="vertical-align:middle;">Yelp Reviews</th>
                                              <th style="vertical-align:middle;">Custom Reviews</th>
        																			<th style="vertical-align:middle;">Created date</th>
                                              <th style="vertical-align:middle;" data-priority="2">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>


																					<?php $sr=0;
																					$c = $db->getUserCampaignsByUserId($_SESSION['user_id'])
																					?>
																					<?php if (@count($c)>0): ?>

																						<?php foreach ($c as $row): ?>

																							<tr>
																								<td><?= ++$sr;?></td>
																								<td><?= $row['title'];?></td>
																								<td><?php if ($row['is_facebook']=="1"): ?><label class="label label-success">Enabled</label><?php else: ?><label class="label label-warning">Disabled</label><?php endif; ?></td>
																								<td><?php if ($row['is_google']=="1"): ?><label class="label label-success">Enabled</label><?php else: ?><label class="label label-warning">Disabled</label><?php endif; ?></td>
																								<td><?php if ($row['is_yelp']=="1"): ?><label class="label label-success">Enabled</label><?php else: ?><label class="label label-warning">Disabled</label><?php endif; ?></td>
																								<td><?php if ($row['is_custom']=="1"): ?><label class="label label-success">Enabled</label><?php else: ?><label class="label label-warning">Disabled</label><?php endif; ?></td>

																									<td><?= $row['created_date']; ?></td>
																									<td>
																											<!--- Start of Action Coloum-->
																						<div class=" btn-group">
																						<a href="/campaign_add?id=<?= $row['id']; ?>" class="btn btn-fill btn-xs btn-info">
																						<i class="ace-icon fa fa-pencil bigger-120"></i>
																						</a>

																						<a onclick="return deleteMe('/action.php?id=<?= $row['id']; ?>&action=delete_camp')" class="btn btn-fill btn-xs btn-danger">
																						<i class="ace-icon fa fa-trash-o bigger-120"></i>
																						</a>
																						</div>
																											<!--- End of Action Colum-->
																						</td>
																						</tr>
																						<?php endforeach; ?>
																					<?php endif; ?>
                                        </tbody>
                </table>
							</div>
						</section>
					<!-- end: page -->
				</section>
			</div>

		</section>

<?php include_once('footer_default.php'); ?>
        <script>


				$('#datatable-responsive').DataTable( {
				    responsive: true,
						searching: false,
						bLengthChange : false,
						ordering: false

				} );
        function deleteMe(url){
            if(confirm("Are you sure?")){
                window.location = url;
            }
            return false;
        }

        </script>

	</body>
</html>
