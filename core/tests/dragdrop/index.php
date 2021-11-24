<?php
@session_start();

if( isset( $_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
{


  $phpFileUploadErrors = array(
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.',
  );

  if ((isset($_FILES['file'])) && (($_FILES['file']['error']!==0) || ($_FILES['file']['tmp_name']==''))) {
    die(json_encode(['success'=>false, 'message'=>$phpFileUploadErrors[$_FILES['file']['error']]]));
  } elseif (isset($_FILES['file']) && $_FILES['file']['tmp_name']!='') {

    require_once('../../import.php');
    $import= new Import();
    try {
        $spreadsheets = $import->getWorkSheetNames($_FILES['file']['tmp_name']);
        $tmp_file_name = 'upload/_'.time().'_'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'],$tmp_file_name);
        $_SESSION['tmp_file_name'] = $tmp_file_name;
    } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        die(json_encode(['success'=>false, 'message'=>$e->getMessage()]));
    }

    if ((isset($spreadsheets)) && (is_array($spreadsheets))) {
     $data = null;

       foreach ($spreadsheets as $value) {
         $data[$value]=$import->readExcelFile($_SESSION['tmp_file_name'],1,1,null,$value);
      }
      $_SESSION['sheet_names'] = $data;
      die(json_encode(['success'=>true, 'data'=>$spreadsheets]));
    }

  }

  if (isset($_POST['sheet_selected'])) {

    die(json_encode(['success'=>true, 'data'=>$_SESSION['sheet_names'][$_POST['sheet_selected']]]));


      die(json_encode(['success'=>true, 'data'=>$data]));
  }

  if (isset($_POST['sheet_selected_import'])&& isset($_SESSION['tmp_file_name'])) {
    $data=$_POST['sheet_selected_import'];


    $startRow = ($data['skip_first_row'] =="false") ? 1 : 2;



    $sheet_name=$data['sheet_name'];
    $options=array_column($data['options'],'value','name');
    $data = null;

    require_once('../../import.php');
    $import= new Import();
    try {
        $spreadsheet = $import->readExcelFile($_SESSION['tmp_file_name'],1,0,array_diff($options, ["0"]),$sheet_name);
    } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        die(json_encode(['success'=>false, 'message'=>$e->getMessage()]));

    }

     $options=array_flip(array_diff($options, ["0"]));
    if ((isset($spreadsheet)) && (is_array($spreadsheet))) {
      foreach ($spreadsheet as $value) {
        $row=[];
        foreach ($value as $k => $v) {
        $row[$options[$k]]=$v;
        }
        $data[] = $row;
      }
    }



    die(json_encode(['success'=>true, 'data'=>$data]));

  }


  // $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
   // var_dump($sheetData);

  die(json_encode(['success'=>false, 'message'=>'error 😕']));

}


 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title></title>




    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xls/0.7.6/xls.min.js" integrity="sha256-k6LY0IL8c1UEP4tUrNC/eNKZEQq2Cdl058Cozf1jjJk=" crossorigin="anonymous"></script>
<script type="text/javascript">

$(function() {

    // preventing page from redirecting
    $(".drag-container").on("dragover", function(e) {
        e.preventDefault();
        e.stopPropagation();
        $("h1").text("Drag here");
    });
    // Drag enter
    $('.drag-container .upload-area').on('dragenter', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $("h1").text("Drop");
    });

    // Drag over
    $('.drag-container .upload-area').on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $("h1").text("Drop");
    });

    // Drop
    $('.drag-container .upload-area').on('drop', function (e) {
        e.stopPropagation();
        e.preventDefault();

        $("h1").text("Upload");

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
        var fd = new FormData();

        var files = $('#file').prop('files')[0];
        fd.append('file',files);
        uploadData(fd);
    });
});

// Sending AJAX request and upload file
function uploadData(formdata){
  $('.result-content').html('');

  console.log('start uploading...');
    $.ajax({

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


          } else {
            ret  = '<div class="alert alert-danger">'+response.message+'</div>';
            $(".result-content").html(ret);

          }
        },
        error: function(e) {
          console.log('error accured ');
          console.log(e);
            }
    });
}

$(document).on('click', '.sheet-button' , function(){
  var sheet_name= $(this).text();
  $.ajax({
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
           text += '<td class="text-center col-md-1"><select name="photo">'+list_fields+'</select></td>';
           text += '<td class="text-center col-md-1"><select name="name">'+list_fields+'</select></td>';
           text += '<td class="text-center col-md-1"><select name="rating">'+list_fields+'</select></td>';
           text += '<td class="text-center col-md-1"><select name="review">'+list_fields+'</select></td>';
           text += '<td class="text-center col-md-1"><select name="date">'+list_fields+'</select></td>';
           text += '<td class="text-center col-md-1"><select name="icon">'+list_fields+'</select></td>';
           text += '<td class="text-center col-md-1"><select name="tags">'+list_fields+'</select></td>';

           text += '</tr>';
           text += '</table>';
           text += '</div>';
           text += '<div class="checkbox skip-first-row"> <label><input name="skip_first_row" type="checkbox">Skip first row</label></div>';
           text += '<button class="btn btn-default sheet-import-button" type="button">Import Data</button>';
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
   options_div=$(this).siblings().filter('[sheet_name]');
  var sheet_name=options_div.attr('sheet_name');
   skip_first_row = $(this).siblings('.skip-first-row').find('input[name="skip_first_row"]').prop('checked');
  var options = [];
   $( options_div ).find('select').each(function( index ) {
     options[index] = {'name': $(this).attr('name'),
                        'value': $(this).val()
                      };
   });
   $.ajax({
          type: "POST",
          dataType: 'json',
          data: {"sheet_selected_import":  {"sheet_name":sheet_name, "skip_first_row":skip_first_row,"options":options} },
          success: function (response) {
            console.log(response);
          }
    });


});


</script>
<style media="screen">
.container{
    width: 50%;
    margin: 0 auto;
}

.upload-area{
    width: 70%;
    height: 200px;
    border: 2px solid lightgray;
    border-radius: 3px;
    margin: 0 auto;
    margin-top: 100px;
    text-align: center;
    overflow: auto;
}

.upload-area:hover{
    cursor: pointer;
}

.upload-area h1{
    text-align: center;
    font-weight: normal;
    font-family: sans-serif;
    line-height: 50px;
    color: darkslategray;
}

#file{
    display: none;
}

/* Thumbnail */
.thumbnail{
    width: 80px;
    height: 80px;
    padding: 2px;
    border: 2px solid lightgray;
    border-radius: 3px;
    float: left;
    margin: 5px;
}

.size{
    font-size:12px;
}
.file-result {
  display: flex;
    justify-content: center;
    /* border: 1px solid gray; */
}

</style>
    </head>
    <body >

        <div class="container drag-container" >

            <input id="file" type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />

            <!-- Drag and Drop container-->
            <div class="upload-area"  id="uploadfile">
                <h1>Drag and Drop file here<br/>Or<br/>Click to select file</h1>
            </div>
        </div>

        <div class="file-result">
          <div class="result-content container">
          </div>
        </div>





    </body>
</html>
