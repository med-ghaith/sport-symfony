<?php

namespace App\Utils;

//require dirname(__DIR__).'\vendor\autoload.php';
//require '../../vendor/autoload.php';
//require __DIR__.'/../../vendor/autoload.php';
use phpoffice\phpspreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory as phpSpreadSheetIOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate as phpSpreadSheetCellCoordinate;

$inputFileType = 'Xlsx';
//$inputFileName = 'ChatBotDataSet.xlsx';
$inputFileName =  __DIR__.'/../../public/ChatBotDataSet.xlsx';

/**  Create a new Reader of the type defined in $inputFileType  **/
//$reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
$reader = phpSpreadSheetIOFactory::createReader($inputFileType);
/**  Advise the Reader that we only want to load cell data  **/
$reader->setReadDataOnly(true);
/**  Load $inputFileName to a Spreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);
$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
$worksheet = $spreadsheet->getSheet(0);//
// Get the highest row and column numbers referenced in the worksheet
$highestRow = $worksheet->getHighestRow(); // e.g. 10
$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
//$highestColumnIndex = PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
$highestColumnIndex = phpSpreadSheetCellCoordinate::columnIndexFromString($highestColumn);

class ExcelToJson
{

    private  $worksheet, $highestColumn, $highestRow;


    public function __construct()
    {

        $inputFileType = 'Xlsx';
        $inputFileName =  __DIR__.'/../../public/ChatBotDataSet.xlsx';

        /**  Create a new Reader of the type defined in $inputFileType  **/
        //$reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $reader = phpSpreadSheetIOFactory::createReader($inputFileType);
        /**  Advise the Reader that we only want to load cell data  **/
        $reader->setReadDataOnly(true);
        /**  Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = $reader->load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        //$worksheet = $spreadsheet->getSheet(0);//
        $this->worksheet= $spreadsheet->getSheet(0);//
        // Get the highest row and column numbers referenced in the worksheet
        //$highestRow = $worksheet->getHighestRow(); // e.g. 10
        $this->highestRow = $this->worksheet->getHighestRow(); // e.g. 10
        //$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $this->highestColumn = $this->worksheet->getHighestColumn(); // e.g 'F'
        //$highestColumnIndex = PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        $highestColumnIndex = phpSpreadSheetCellCoordinate::columnIndexFromString($this->highestColumn);

    }


    /*
     * returns the column index of the given name
     * returns -1 if there is no column
    */
    function findColumnName($name)
    {
        //global $highestColumn;
        //global $worksheet;

        //$highestColumn++;
        $this->highestColumn++;
        $row = 1;
        for ($col = 'A'; $col != $this->highestColumn; $col++) {
            if ($this->worksheet->getCell($col . $row) == $name) {
                //return PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col);
                return phpSpreadSheetCellCoordinate::columnIndexFromString($col);
            }
        }
        return -1;
    }


    function getColumnByName($name)
    {
        $data = array();
        //global $highestRow;
        //global $worksheet;
        $col = $this->findColumnName($name);
        if ($col == -1) {
            return $data;
        }
        for ($row = 2; $row <= $this->highestRow; $row++) {
            if ($this->worksheet->getCellByColumnAndRow($col, $row)->getValue() != null) {
                $data[] = $this->worksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $data;
    }

    function convertColumnArrayToJson($name)
    {
        $data = $this->getColumnByName($name);
        $arr = array($name => $data);
        return json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }

    function convertColumnArrayToJsonForJSBot($name)
    {
        $data = $this->getColumnByName($name);
        $arr = array($name => $data);
        return json_encode($arr);
    }


    function convertAllXclToJson()
    {
        //global $highestColumn;
        //global $worksheet;
        $this->highestColumn++;
        $row = 1;
        for ($col = 'A'; $col != $this->highestColumn; $col++) {
            //$colIndex = PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col);
            $colIndex = phpSpreadSheetCellCoordinate::columnIndexFromString($col);
            $name = $this->worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();
            if (!($name == "")) {
                $data = $this->getColumnByName($name);
                $arr[] = array($name => $data);

            }
        }
        echo json_encode($arr);
    }
}
//
//$jaw = new ExcelToJson();
//$jaw->convertAllXclToJson();


