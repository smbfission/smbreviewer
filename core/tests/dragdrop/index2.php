<?php


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

 <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.6/xlsx.full.min.js" integrity="sha256-fImCm6IrKW/Y9BpJdSEhgQNnco1ccwp1d7Mhjwn1Zrs=" crossorigin="anonymous"></script>

<style media="screen">

.drag-container {

    display: flex;
    justify-content: center;

}
.drag-container .upload-area {
  height: 200px;
      display: flex;
      justify-content: center;
      align-items: center;
  }

.file-result {
  display: flex;
    justify-content: center;
    border: 1px solid green;
}
</style>
   </head>
   <body>


<div class="container drag-container" >

  <div class="upload-area text-center col-md-4 bg-info" id="uploadfile">
      Drag and Drop file here
  </div>

</div>

<div class="container">
  <div class="row">

    <div class="file-result col-md-offset-2 col-xs-12 col-md-8">
      <div class="container">
        <div class="row">
          <div class="result-content col-xs-12"></div>
        </div>
      </div>
    </div>



    </div>

</div>

<script type="text/javascript">

var workbook;
var foo;
$(function() {

  // preventing page from redirecting
  $(".drag-container").on("dragover", function(e) {
      e.preventDefault();
      e.stopPropagation();

  });

  $(".drag-container").on("drop", function(e) {

     $('.result-content').html('');
    e.stopPropagation(); e.preventDefault();
     // console.log(e.originalEvent.dataTransfer);
     var files = e.originalEvent.dataTransfer.files, f = files[0];

     switch(true)
    {
      case f.type == 'application/vnd.ms-excel':
      case f.type == 'text/csv':
      case f.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
      case f.name.endsWith('.xls'):

      var reader = new FileReader();
      reader.onload = function(e) {
        var data = new Uint8Array(e.target.result);
         workbook = XLSX.read(data, {type: 'array',sheets: 0, sheetRows: 0 });


         var text = '<div class="text-center col-md-12">Please select worksheet</div><div class="col-md-12">';

         $.each( workbook.SheetNames, function( i, val ) {
          text += '<button class="btn btn-default sheet-button" type="button">'+val+'</button>';
         });

         text += '</div>';

         $('.result-content').html(text);



      };
      reader.readAsArrayBuffer(f);


      break;
      default:
           console.log(f.type);
           console.log(f.name);
           foo=f.name;
      break;
    }


  });

$(document).on('click','.sheet-button',function() {

console.log($(this).text());


json = XLSX.utils.sheet_to_json(workbook.Sheets[$(this).text()], {
      header: 1
    });
    var list_fields  = '<select>';
     $.each( json[0], function( i, val ) {
      list_fields += '<option>'+val+'</option>';
    });
    list_fields  += '</select>';

   var text = '<div class="text-center col-md-12">Please match columns</div>';
   text += '<div class="text-center col-md-12"> <table class="table">'
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
    text += '<td class="text-center col-md-1">'+list_fields+'</td>';
    text += '<td class="text-center col-md-1">'+list_fields+'</td>';
    text += '<td class="text-center col-md-1">'+list_fields+'</td>';
    text += '<td class="text-center col-md-1">'+list_fields+'</td>';
    text += '<td class="text-center col-md-1">'+list_fields+'</td>';
    text += '<td class="text-center col-md-1">'+list_fields+'</td>';
    text += '<td class="text-center col-md-1">'+list_fields+'</td>';

    text += '</tr>';


    text += '</table>';
    text += '</div>';

   //


   $('.result-content').append(text);


console.log();

});

});


</script>
   </body>
 </html>
