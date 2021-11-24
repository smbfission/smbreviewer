<?php
include_once("header.php");
include_once("sidebar.php");
?>

<!-- <link rel="stylesheet" href="assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.css" /> -->
          <section role="main" class="content-body">
					<header class="page-header">
						<h2>Import file instructions</h2>

					</header>

          <div class="row">
						<div class="col-lg-12">
							<section class="panel">
								<div class="panel-body">

                  <div class="row">
                    <div class="col-sm-12">
            					<h1 class="h2 mt-none mb-sm text-dark text-bold">Instructions</h1>
            					<p class="text-muted font-13 m-b-15">
                                <!-- Description Goes Here <br /> -->
                                              &nbsp;
                          </p>
            							</div>
                  <?php if (isset($_SESSION['msg']) && $_SESSION['msg']!=""): ?>
                    <div class="col-sm-12">
                        <div class="alert alert-<?= (isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : 'info')  ?>"><?= $_SESSION['msg'] ?></div>
                    </div>
                  <?php endif; ?>
                  <?php unset($_SESSION['msg']); ?>
                  </div>





                         </div>
                       </section>
                        </div>
                    </div>
                </section>



        <!-- END wrapper -->


<?php include_once('footer_default.php'); ?>

    </body>
</html>
