<?php
include_once("header.php");
include_once("sidebar.php");
?>

<style media="screen">


.img-container i.fa {
  position: absolute;
      left: 8.2rem;
      
      margin-top: -3px;
      color: green;

}

</style>
                <section role="main" class="content-body">
					<header class="page-header">
						<h2>Your Reviews</h2>

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
                                        Your Custom Reviews
                              <a style="margin-bottom:5px;" class="btn btn-fill btn-primary pull-right" type="submit" href="/custom_reviews_add/">Add Custom Reviews</a>

                              <button style="margin-bottom:5px;margin-right:5px;" class="btn btn-fill btn-info pull-right import-reviews" type="button" data-toggle="modal" data-target="#import_reviews">Import reviews</button>
                        </h2>
        								<p class="text-muted font-13 m-b-15">
                                            <!-- Description Goes Here <br /> -->
                                            These are the reviews that you added manually, captured, or uploaded in bulk. These do not include reviews that are dynamically populated via the Google, Facebook, and Yelp integrations for the time-being. 
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
                                          <th data-priority="1">Review No.</th>
                                          <th style="vertical-align:middle;white-space: normal;">Reviewer Picture</th>
                                          <th style="vertical-align:middle;white-space: normal;">Reviewer Name</th>
                                        	<th style="vertical-align:middle;white-space: normal;">Rating</th>
                                          <th style="vertical-align:middle;white-space: normal;">Type</th>
                                        	<th style="vertical-align:middle;white-space: normal;">Review</th>
    										                  <th style="vertical-align:middle;white-space: normal;">Date</th>
                                          <th style="vertical-align:middle;white-space: normal;" data-priority="2">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
    									$sql = "SELECT * from custom_reviews where user_id = '".$_SESSION['user_id']."'";
										$result = $conn->query($sql);
                                        $records = $result->num_rows;

										if ($records > 0) {
										  $sr = 1;
											while($row = $result->fetch_assoc()) {
											?>

                                    <tr>
                                    	<td><?php echo $sr++;?></td>
                                    	<td>
                                        <div class="img-container">


                                        <?php if (trim($row['photo'])==""): ?>
                                        <img src="/uploads/default_files/default_custom_review_user_picture.png" style="max-height: 25px;" />
                                        <?php else: ?>
                                        <img src="<?= (filter_var($row['photo'], FILTER_VALIDATE_URL) ? $row['photo'] : "/uploads/".$row['photo'] )  ?>" style="max-height: 25px;" />
                                          <?=(trim($row['facebook_id'])!=''? '<i class="fa fa-check-circle"></i>': '') ?>
                                        <?php endif; ?>


                                        </div>

                                      </td>
                                        <td><?php echo $row['name'];?></td>
                                    	<td><?php echo $row['rating']; ?></td>
                                      <td><?php echo empty($row['loom_url']) ? '' : '<img style="width: 25px;" src="/assets/img/video_icon.png" alt="video">'; ?></td>
                                        <td><?php echo $row['review']; ?></td>
                                        <td><?php echo $row['date']; ?></td>
                                        <td>
                                            <!--- Start of Action Coloum-->
											<div class=" btn-group">
												<a href="/custom_reviews_add?id=<?php echo $row['id']; ?>" class="btn btn-fill btn-xs btn-info">
													<i class="ace-icon fa fa-pencil bigger-120"></i>
												</a>

												<a onclick="return deleteMe('/action.php?id=<?php echo $row['id']; ?>&action=delete_review')" class="btn btn-fill btn-xs btn-danger">
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



                    </div> <!-- container -->


                    </section>
					<!-- end: page -->
				</section>
			</div>

		</section>

    <div class="modal fade" tabindex="-1" role="dialog" id="import_reviews" aria-labelledby="myLargeModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

         <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="exampleModalLabel">Import custom reviews</h4>


         </div>
         <div class="modal-body">
           <div class="row display-flex">

             <div class="drag-container" >

                 <input id="file" type="file" class="hidden" accept=".csv, .xls, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                 <div class="upload-area text-center"  id="uploadfile">
                     <h3>Drag and Drop file here<br/>Or<br/>Click to select file</h3>
                 </div>
             </div>

             <div class="file-result">
               <div class="result-content col-md-12">
               </div>
             </div>

         </div>
       </div>
         <div class="modal-footer">
    <a class="btn btn-info pull-left"  href="/uploads/default_files/sample_import_file.xls" download>Download Sample File</a>
    <a class="btn btn-primary pull-left" target="_blank" href="/bulk-upload-instructions.php" >Instructions</a>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

    </div>
    </div>




    </div>

    </div>

<?php include_once('footer_default.php'); ?>

    <script type="text/javascript">



        $('#datatable-responsive').DataTable( {
            responsive: true,
            searching: true,
            bLengthChange : false,
            ordering: false,

        } );


        $('#datatable-responsive thead tr:eq(1) th').each( function (i) {
          console.log(i);
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

        function deleteMe(url){
            if(confirm("Are you sure?")){
                window.location = url;
            }
            return false;
        }


        $(function() {

            origin_text = $(this).find("h3").html();

            // preventing page from redirecting
            $(".drag-container").on("dragover", function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).find("h3").text("Drag here");
            });
            // Drag enter
            $('.drag-container .upload-area').on('dragenter', function (e) {
                e.stopPropagation();
                e.preventDefault();
                $(this).find("h3").text("Drop");
            });

            // Drag over
            $('.drag-container .upload-area').on('dragover', function (e) {
                e.stopPropagation();
                e.preventDefault();
                $(this).find("h3").text("Drop");
            });

            // Drop
            $('.drag-container .upload-area').on('drop', function (e) {
                e.stopPropagation();
                e.preventDefault();

                $(this).find("h3").text("Uploading...");

                var file = e.originalEvent.dataTransfer.files;
                var fd = new FormData();

                fd.append('file', file[0]);

                uploadData(fd);
            });

            // Open file selector on div click
            $("#uploadfile").click(function(){

                $("#file").click();

            });

            // file selected
            $("#file").change(function(){
                $('.drag-container .upload-area h3').text("Uploading...");
                var fd = new FormData();
                var files = $('#file').prop('files')[0];
                fd.append('file',files);
                uploadData(fd);
            });
        });



        // Sending AJAX request and upload file
        function uploadData(formdata){

          $(".result-content").html('');
            $.ajax({
                url:'/action.php?action=import_reviews',
                type: 'post',
                data: formdata,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response){
                  var ret='';
                  if (response.success) {
                    var text = '<div class="text-center col-md-12">Please select worksheet</div><div class="col-md-12">';

                    $.each( response.data, function( i, val ) {
                     text += '<button class="btn btn-default sheet-button" type="button">'+val+'</button>';
                    });
                    text += '</div>';
                    $('.result-content').html(text);
                    $('.drag-container .upload-area h3').text("Uploaded");

                  } else {
                    ret  = '<div class="alert alert-danger">'+response.message+'</div>';
                    $(".result-content").html(ret);
                    $('.drag-container .upload-area h3').html(origin_text);
                  }
                },
                error: function(e) {
                   ret  = '<div class="alert alert-danger">General upload error. try again later</div>';
                   $(".result-content").html(ret);
                  $('.drag-container .upload-area h3').html(origin_text);
                    }
            });




        }

        $(document).on('click', '.sheet-button' , function(){
          var sheet_name= $(this).text();
          $.ajax({
                 url:'/action.php?action=import_reviews',
                 type: "POST",
                 dataType: 'json',
                 data: {"sheet_selected":  sheet_name },
                 success: function (response) {

                   $('.table-match').remove();

                   var list_fields = '<option value=0>- ignore -</option>';
                    $.each( response.data[1], function( i, val ) {

                     list_fields += '<option value="'+i+'">'+i+'|'+val+'</option>';
                   });


                  var text = '<div class="table-match"><div class="text-center col-md-12">Please match columns</div>';
                  text += '<div class="text-center col-md-12 table-responsive" sheet_name="'+sheet_name+'"> <table class="table">'
                  text += '<tr>';
                   text += '<td class="text-center col-md-1">Review Picture</td>';
                   text += '<td class="text-center col-md-1">Reviewer Name</td>';
                   text += '<td class="text-center col-md-1">Rating</td>';
                   text += '<td class="text-center col-md-1">Review Content</td>';
                   text += '<td class="text-center col-md-1">Review Date</td>';
                   text += '<td class="text-center col-md-1">Review icon</td>';
                   text += '<td class="text-center col-md-1">Tags</td>';
                   text += '</tr>';

                   text += '<tr>';
                   text += '<td class="text-center col-md-1"><select class="form-control" name="photo">'+list_fields+'</select></td>';
                   text += '<td class="text-center col-md-1"><select class="form-control" name="name">'+list_fields+'</select></td>';
                   text += '<td class="text-center col-md-1"><select class="form-control" name="rating">'+list_fields+'</select></td>';
                   text += '<td class="text-center col-md-1"><select class="form-control" name="review">'+list_fields+'</select></td>';
                   text += '<td class="text-center col-md-1"><select class="form-control" name="date">'+list_fields+'</select></td>';
                   text += '<td class="text-center col-md-1"><select class="form-control" name="icon">'+list_fields+'</select></td>';
                   text += '<td class="text-center col-md-1"><select class="form-control" name="temp_tags">'+list_fields+'</select></td>';

                   text += '</tr>';
                   text += '</table>';
                   text += '</div>';
                   text += '<div class="col-md-12 checkbox skip-first-row"> <label><input name="skip_first_row" type="checkbox">Skip first row</label></div>';
                   text += '<div class="col-md-12"><button class="btn btn-default sheet-import-button" type="button">Import Data</button></div>';
                   text += '</div>';


                 // text = $'<td class="text-center col-md-1">'+$(list_fields).find('option').eq(0).prop('selected', true).text()+'</td>';
                 text = $(text);

                 $( text ).find('select').each(function( index ) {
                   $( this ) .find('option').eq((index+1)).prop('selected',true);

                  });
                 $('.result-content').append(text);

                 }
             });
        });

        $(document).on('click', '.sheet-import-button' , function(){
          var options_div=$('.result-content div[sheet_name]');
          var sheet_name=options_div.attr('sheet_name');
          var skip_first_row = options_div.parent().find('input[name="skip_first_row"]').prop('checked');
          var options = [];
          options_div.find('select').each(function( index ) {
          options[index] = {'name': $(this).attr('name'),
                            'value': $(this).val()
                          };
           });
          $.ajax({
                  url:'/action.php?action=import_reviews',
                  type: "POST",
                  dataType: 'json',
                  data: {"sheet_selected_import":  {"sheet_name":sheet_name, "skip_first_row":skip_first_row,"options":options} },
                  success: function (response) {

                    if (response.success) {
                      ret  = '<div class="alert alert-success">'+response.message+'</div>';
                      $(".result-content").html(ret);
                      $('.drag-container .upload-area h3').html(origin_text);

                    } else {
                      ret  = '<div class="alert alert-danger">'+response.message+'</div>';
                      $(".result-content").html(ret);
                      $('.drag-container .upload-area h3').html(origin_text);
                    }
                  }
            });


        });





    </script>


	</body>
</html>
