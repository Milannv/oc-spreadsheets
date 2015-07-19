<?php

/**
 * ownCloud - spreadsheets
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Milann Veldink <info@milannveldink.com>
 * @copyright Milann Veldink 2015
 */

namespace OCA\Spreadsheets;

use PHPExcel;
use PHPExcel_Worksheet;
use PHPExcel_IOFactory;

require_once realpath(dirname(__FILE__)) . '/../vendor/PHPExcel/Classes/PHPExcel.php';
require_once realpath(dirname(__FILE__)) . '/../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class AbstractWriter {
    
    protected $metaData;
    protected $sheetNames;
    protected $objPHPExcel;
    protected $objWriter;
    protected $contents;
    protected $exportFormat;
    protected $exportPath;
    protected $supportedExportTypes = ['Excel2007', 'Excel5'];
    
    /**
     * 
     * @param type $contents
     * @param type $exportFormat
     */
    public function __construct($contents,$exportFormat=null) {        
        $this->contents = $contents;

        if(!$exportFormat) {
            $this->exportFormat = $this->determineWriter(
                    $this->contents['metaData']['full_path']
                    );
        } else {
            $this->exportFormat = $exportFormat;
        }
        
        $this->objPHPExcel = new PHPExcel();
    }

    /**
     * This code is taken from the PHPExcel library, IOFactory.php file with
     * method name "createReaderForFile"
     * 
     * @param type $filename
     */
    public static function determineWriter($filename) {
        $pathinfo = pathinfo($filename);

        $extensionType = null;
        if (isset($pathinfo['extension'])) {
            switch (strtolower($pathinfo['extension'])) {
                case 'xlsx':            //    Excel (OfficeOpenXML) Spreadsheet
                case 'xlsm':            //    Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
                case 'xltx':            //    Excel (OfficeOpenXML) Template
                case 'xltm':            //    Excel (OfficeOpenXML) Macro Template (macros will be discarded)
                    $extensionType = 'Excel2007';
                    break;
                case 'xls':                //    Excel (BIFF) Spreadsheet
                case 'xlt':                //    Excel (BIFF) Template
                    $extensionType = 'Excel5';
                    break;
                case 'ods':                //    Open/Libre Offic Calc
                case 'ots':                //    Open/Libre Offic Calc Template
                    $extensionType = 'OOCalc';
                    break;
                case 'slk':
                    $extensionType = 'SYLK';
                    break;
                case 'xml':                //    Excel 2003 SpreadSheetML
                    $extensionType = 'Excel2003XML';
                    break;
                case 'gnumeric':
                    $extensionType = 'Gnumeric';
                    break;
                case 'htm':
                case 'html':
                    $extensionType = 'HTML';
                    break;
                case 'csv':
                    // Do nothing
                    // We must not try to use CSV reader since it loads
                    // all files including Excel files etc.
                    break;
                default:
                    break;
            }
        }
        
        return $extensionType;
    }
    
    /**
     * 
     * @param string $format
     */
    public function setExportFormat($format)
    {
        $this->exportFormat = $format;
    }
    
    /**
     * 
     * @return integer
     */
    protected function getSheetAmount() {
        return count($this->contents['sheets']);
    }
    
    /**
     * 
     * @return void
     */
    protected function prepareFileContent() {
        $currentRow = 1;
        $currentCol = 0;
        
        foreach($this->contents['sheets'] as $sheetName => $data)
        {
            if(!$this->objPHPExcel->sheetNameExists($sheetName)) {
                $this->objPHPExcel
                    ->addSheet(new PHPExcel_Worksheet(null, $sheetName))
                    ->setTitle($sheetName);
            }
            
            // Reset row to 1 (start position)
            $currentRow = 1;
            
            // Iterate through sheet data
            foreach($data as $index => $values) {
                $worksheet = $this->objPHPExcel->setActiveSheetIndexByName($sheetName);
                
                for ($i = 0; $i < count($values); $i++) {
                    $value = $values[$i];
                    
                    $worksheet->setCellValueByColumnAndRow($currentCol++, $currentRow, $value);
                    
                    if($i === count($values) - 1)
                    {
                        // Reset current col to zero, increment currentRow with 1
                        $currentCol = 0;
                        $currentRow++;
                    }
                }
            }
        }
        
        // Check if our sheets contained a sheet called "Worksheet". If not,
        // we delete it
        if(is_array($this->sheetNames) && !array_key_exists('Worksheet', $this->sheetNames)) {
            // Lookup sheet by name and retrieve index. PHPExcel currently 
            // doesn't support deleting a sheet by name
            $this->objPHPExcel->setActiveSheetIndexByName('Worksheet');
            $index = $this->objPHPExcel->getActiveSheetIndex();
            
            $this->objPHPExcel->removeSheetByIndex($index);
        }
    }
    
    /**
     * 
     * @return \OCA\Spreadsheets\SheetExporter
     */
    protected function appendMetaData() {
        $this->objPHPExcel->getProperties()
                ->setCreator($this->metaData['author'])
                ->setCompany($this->metaData['company'])
                ->setLastModifiedBy($this->metaData['last_modified_by'])
                ->setCreated($this->metaData['created_at'])
                ->setModified($this->metaData['modified_at'])
                ->setTitle($this->metaData['title'])
                ->setDescription($this->metaData['description'])
                ->setKeywords($this->metaData['keywords']);
        
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    protected function isSupportedWriteFormat() {
        return in_array($this->exportFormat, $this->supportedExportTypes);
    }
    
    /**
     * 
     * @param array $sheets
     */
    public function setSheetNames($sheets) {
        $this->sheetNames = $sheets;
    }
    
    /**
     * 
     * @param array $data
     */
    public function setMetaData($data) {
        $this->metaData = $data;
    }
    
    /**
     * 
     * @return \OCA\Spreadsheets\PHPExcel_Reader_Exception
     */
    public function generate() {
        $this->appendMetaData()->prepareFileContent();
        
        try {
            $this->objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, $this->exportFormat);
        } catch (PHPExcel_Reader_Exception $ex) {
            return $ex;
        }
    }
}