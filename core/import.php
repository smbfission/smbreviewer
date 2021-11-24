<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class MyReadFilter implements IReadFilter
{
    private $startRow = 0;

    private $endRow = 0;

    private $columns = [];

    public function __construct($startRow, $endRow, $columns)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
        $this->columns = $columns;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        if (($row >= $this->startRow) && (($row <= $this->endRow || $this->endRow ==0))) {
            if (in_array($column, $this->columns)) {
                return true;
            }
        }

        return false;
    }
}

/**
 * import data
 */
class Import
{


public function getWorkSheetNames($file)
{

  $fileType = IOFactory::identify($file);
  $reader = IOFactory::createReader($fileType);

  if (strtolower($fileType) != 'csv') {
    return $reader->listWorksheetNames($file);
  }
  return ['Worksheet'];
}


 public function readExcelFile($file, $startRow =1, $endRow =1, $columns =[], $worksheetName = '' )
 {
   if (empty($columns)) {
     $columns = range('A', 'Z');
   }

   $filterSubset = new MyReadFilter($startRow, $endRow, $columns);
   $fileType = IOFactory::identify($file);
   $reader = IOFactory::createReader($fileType);
   $reader->setReadDataOnly(true);
   if ($worksheetName !='') {
     $reader->setLoadSheetsOnly($worksheetName);
   }

   $reader->setReadFilter($filterSubset);
   $spreadsheet =  $reader->load($file)->getActiveSheet();

   return $spreadsheet->toArray(null, true, true, true);

 }


}



 ?>
