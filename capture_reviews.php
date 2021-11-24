<?php
        include_once("header.php");
        include_once("sidebar.php");
        require_once('core/tools.php');



        ?>



<section role="main" class="content-body">
  <header class="page-header">
    <h2>Capture Review Funnels</h2>
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
                                    Review Capture Campaigns


                                </h2>
                <p class="text-muted font-13 m-b-15">
                  Use this tool to collect reviews and emails from your customers in real-time, then you can guide your customers to leave reviews on popular directories as well as display these reviews on your website in real-time. Start by creating a <a href="/capture_reviews_add/">Capture Review Campaign</a>.
                                    &nbsp;
                                </p>
                                <a style="margin-bottom:5px;" class="btn btn-fill btn-primary pull-left" type="submit" href="/capture_reviews_add/">Add New Capture Review Campaign</a>&nbsp;&nbsp;
                                <a style="margin-bottom:5px;margin-left:5px;" class="btn btn-fill btn-primary pull-left" type="submit" target ="_blank" href="http://tutorials.smbreviewer.com/l/m3r74rw577-r69t1skjlu">Tutorial</a>
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


        <table  class="table  display table-striped table-colored table-info nowrap" cellspacing="0"
                                   style="font-size:12px;width:100%">
                                <thead>
                                    <tr >
                                      <th class="text-center">#</th>
                                      <th class="text-center">Title</th>
                                      <th class="text-center" >Tags</th>
                                      <th class="text-center">Created date</th>
                                      <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>


                                  <?php $sr=0;
                                  $c = $db->getCaptureReviewsByUserId($_SESSION['user_id'])
                                  ?>
                                  <?php if (@count($c)>0): ?>

                                    <?php foreach ($c as $row): ?>

                                    <tr class="">
                                        <td  class="actions text-center"><?= ++$sr;?></td>
                                        <td  class="actions text-left campaign-title"><?= $row['title'];?></td>
                                        <td  class="actions text-left"><?php foreach (@explode(',',$row['tags']) as $tag): ?>
                                          <span class="btn btn-primary btn-xs "><?=$tag?></span>
                                        <?php endforeach; ?></td>
                                        <td  class="actions text-center"><?= $row['date_add']; ?></td>
                                        <td  class="actions text-center" capture_id="<?= Tools::encrypt_link($row['id']) ?>">
                                          <!--- Start of Action Coloum-->
                                          <div class="btn-group action-buttons">
                                            <div class="col-12 mb-xs text-nowrap">

                                            <a href="/capture_reviews_add?id=<?= $row['id']; ?>" class="btn btn-fill btn-xs btn-info action-edit">
                                              <i class="ace-icon fa fa-pencil bigger-120"></i>
                                            </a>
                                            <a class="btn btn-fill btn-xs btn-link action-link" data-toggle="modal" data-target="#copy_link_modal" >
                                              <i class="ace-icon fa fa-link bigger-120"></i>
                                            </a>
                                            <a onclick="return deleteMe('/action.php?id=<?= $row['id']; ?>&action=delete_capture_reviews')" class="btn btn-fill btn-xs btn-danger action-delete">
                                              <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                            </a>
                                            </div>

                                            <div class="col-12 mb-xs text-nowrap">

                                              <a onclick="exportMe('<?= $row['id'] ?>',this)" class="btn btn-fill btn-xs btn-prev action-preview">
                                                <i class="ace-icon fa fa-download bigger-120"></i>

                                              </a>
                                              <a  class="btn btn-fill btn-xs btn-code acton-src" data-toggle="modal" data-target="#src_modal">
                                                <i class="ace-icon fa fa-code bigger-120"></i>
                                              </a>
                                              <a onclick="return cloneMe('/action.php?id=<?= $row['id']; ?>&action=clone_capture_reviews')"  class="btn btn-fill btn-xs btn-clone action-clone">
                                                <i class="ace-icon fa fa-files-o bigger-120"></i>
                                              </a>
                                            </div>


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


<!-- copy link  modal -->

<div  id="copy_link_modal" class="modal fade copy-link-modal" tabindex="-1" role="dialog" >
  <div class="modal-dialog modal-lg" role="document">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" >Link to campaign </h4>
      </div>
      <div class="modal-body">
        <div class="input-group mb-sm">
        <input type="text" value=""  class="form-control">
        <div class="input-group-addon" data-toggle="tooltip" data-placement="top" title="copy link to clipboard">
         <span class="do-cpy"><i class="fa fa-clipboard" aria-hidden="true"></i></span>
        </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<!-- src  modal -->

<div  id="src_modal" class="modal fade src-link-modal" tabindex="-1" role="dialog" >
  <div class="modal-dialog modal-lg" role="document">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" >Embed Code on  Your Site </h4>
      </div>
      <div class="modal-body">
        <div class="input-group mb-sm">
        <textarea class="form-control" rows="5">
        </textarea>
        <div class="input-group-addon" data-toggle="tooltip" data-placement="top" title="copy to clipboard">
         <span class="do-cpy"><i class="fa fa-clipboard" aria-hidden="true"></i></span>
        </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<?php include_once('footer_default.php'); ?>
<script src="/assets/vendor/jquery-awesome-cursor/jquery.awesome-cursor.min.js" ></script>

<script type="text/javascript">

 //window.jQuery = window.$ = jQuery_2_1_0;



$(document).on('click','.action-buttons a',function() {
  console.log('clickeddd',$(this).attr('data-target'));
  if ($(this).attr('data-target')=='#copy_link_modal') {
    $('#copy_link_modal').find('input').val('<?=Tools::siteURL().'/crc/'?>'+$(this).closest('td').attr('capture_id')) ;

  }

  if ($(this).attr('data-target')=='#src_modal') {

      $('#src_modal').find('textarea').text('<iframe src="<?=Tools::siteURL().'/crc/'?>'+$(this).closest('td').attr('capture_id')+' "frameborder="0" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px" height="100%" width="100%"></iframe>') ;
  }

  });



$('#copy_link_modal').on('show.bs.modal', function (e) {





});

$(document).on('click','#copy_link_modal .do-cpy',function() {
console.log('copping ');
  var $temp = $("<input>");
     $("body").append($temp);
     $temp.val($('#copy_link_modal input').val()).select();
     $temp.focus();
     document.execCommand("copy");
     $temp.remove();



});



$('#src_modal').on('show.bs.modal', function (e) {
   var v =$(e.relatedTarget);





});


$(document).on('click','#src_modal .do-cpy',function() {

  var $temp = $("<input>");
     $("body").append($temp);
     $temp.val($('#src_modal textarea').text()).select();
     $temp.focus();
     document.execCommand("copy");
     $temp.remove();



});





function deleteMe(url){
    if(confirm("Are you sure want to delete campaing?")){
        window.location = url;
    }
    return false;
}

function cloneMe(url){
    if(confirm("Are you sure want to duplicate campaing ?")){
        window.location = url;
    }
    return false;
}

function exportMe(id, e) {

  $.post( "/action.php", {'action':'export_capture_reviews','id':id,'user_id':'<?=$_SESSION['user_id']?>'},
    function( data ) {
      data = $.parseJSON( data );

      if( (data != null ) && ('file' in data)) {
        var $a = $("<a>");
        var fn = $(e).closest('tr').find('td.campaign-title').text();

           $a.attr("href",data.file);
           $("body").append($a);
           $a.attr("download",fn+".xls");
           $a[0].click();
           $a.remove();
      }

         //return ;

    //   console.log(data);
     });

}

$(document).ready(function(){

$('.do-cpy').awesomeCursor('clipboard',{color: '#008000'});

$('#datatable-responsive').DataTable( {
    responsive: true,
    searching: false,
    bLengthChange : false,
    ordering: false

} );


});


	// console.log('Script using original or the only version: ', jQuery.fn.jquery);




</script>

</body>
</html>
