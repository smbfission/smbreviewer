<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;



/**
 * import data
 */
class Export
{




 public static function exportToExcel($data)
 {


   $header = [];
foreach (array_keys($data[0]) as $value) {
  $header[$value] = $value;
}

   $header = [$header];
   $header = array_merge($header, $data);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray($header, NULL, 'A1');


    $writer = new Xlsx($spreadsheet);


    ob_start();
    $writer->save('php://output');
    $xlsData = ob_get_contents();
    ob_end_clean();

      $response =  array(
        'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
    );

    return $response;



 }


}



 ?>
