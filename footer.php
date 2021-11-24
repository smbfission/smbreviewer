<footer class="footer text-right">
    <!-- 2018 © Product Management-->
</footer>


<script src="../plugins/bootstrap-sweetalert/sweet-alert.min.js"></script>
<!-- Toastr js -->
<script src="../plugins/toastr/toastr.min.js"></script>
<!-- Toastr init js (Demo)-->
<script src="/assets/pages/jquery.toastr.js"></script>

<script>
function deleteRow(x){
    swal({
        title: "Are you sure?",
        text: "Are you sure to Permanently Delete?!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, Please Delete!",
        cancelButtonText: "No Thanks!",
        closeOnConfirm: false
    }, function (isConfirm) {
        if (isConfirm) {
            $("#ajax-loader").show();
            var id  = $(x).attr('id');
            var param = $(x).attr('data-type');
            var action = "delete";

            var url = "action.php";
            $.post(url,{id:id, param:param, action:action},function(res){
                var data = JSON.parse(res);
                if(data.success==1){
                    //swal("Completed!", "Deleted successfully!", "success");
                    swal.close();


                    Command: toastr["success"]("Deleted successfully!")

                    toastr.options = {
                      "closeButton": false,
                      "debug": false,
                      "newestOnTop": false,
                      "progressBar": false,
                      "positionClass": "toast-top-right",
                      "preventDuplicates": false,
                      "onclick": null,
                      "showDuration": "300",
                      "hideDuration": "1000",
                      "timeOut": "5000",
                      "extendedTimeOut": "1000",
                      "showEasing": "swing",
                      "hideEasing": "linear",
                      "showMethod": "fadeIn",
                      "hideMethod": "fadeOut"
                    }

                    $("#row-"+id).remove();
                }else if(data.error==1){
                    swal("Error!", "Action failed!", "danger");
                }
                $("#ajax-loader").hide();
            });
        }
    })
}

function multi_delete(param){
    var ids = $('input[name="delete[]"]:checked').map(function() {return this.value;}).get().join(',')

    var url = "action.php";
    var action = "delete";
    $.post(url,{id:ids, param:param, action:action},function(res){
        var data = JSON.parse(res);
        if(data.success==1){
            window.location = "";
        }
    })


}


function selectAll(){
    var ss = $('input[name="delete[]"]').prop("checked");
    if(ss==false){
        $('input[name="delete[]"]').prop("checked",true);
    }else{
        $('input[name="delete[]"]').prop("checked",false);
    }
}


<?php
if(!isset($_SERVER["HTTPS"])){
    ?>
    var alertmsg = '<div class="alert alert-danger" style="margin: 10px -7px;">Warning : Domian in not secure as SSL is not enabled, Please run on SSL (https) secured domain otherwise application will not work.</div>';
    $(".content-page .container").prepend(alertmsg);
    <?php
}
?>


</script>
