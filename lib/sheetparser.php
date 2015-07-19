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

use PHPExcel_IOFactory;

require_once realpath(dirname(__FILE__)) . '/../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

class SheetParser {

    protected $objReader;
    protected $objPHPExcel;
    protected $data;
    protected $inputFileType;
    protected $inputFilePath;
    protected $filename;

    /**
     * 
     * @param type $file
     * @param type $filename
     * @param type $inputFileType
     */
    public function __construct($file, $filename, $inputFileType=null) {
        $this->inputFilePath = $file;
        $this->filename = $filename;
        
        $this->inputFileType = $inputFileType;
    }

    /**
     * 
     * @return \OCA\Spreadsheets\SheetParser|array
     */
    public function readFile() {
        try {
            if($this->inputFileType)
            {
                $this->objReader = PHPExcel_IOFactory::createReader(
                        $this->inputFileType
                        );
            }
            else
            {
                $this->objReader = PHPExcel_IOFactory::createReaderForFile(
                        $this->inputFilePath
                        );
            }
            $this->objReader->setLoadAllSheets();
            $this->objPHPExcel = $this->objReader->load($this->inputFilePath);
        } catch (Exception $e) {
            return [
               'error' => $e->getMessage(),
            ];
        }

        return $this;
    }

    /**
     * 
     */
    protected function generateDataResponse() {
        $this->iterateThroughSheetNames()
                ->iterateThroughWorksheets()
                ->getSheetNames()
                ->handleMetaData();
    }

    /**
     * 
     * @return array
     */
    public function getDataResponse() {
        if(!$this->data)
        {
            $this->generateDataResponse();
        }
        
        return $this->data;
    }

    /**
     * 
     * @return \OCA\Spreadsheets\SheetParser
     */
    protected function iterateThroughSheetNames() {
        foreach ($this->objPHPExcel->getSheetNames() as $sheet) {
            $this->data['sheets'][$sheet] = [];
        }

        return $this;
    }
    
    /**
     * 
     * @return \OCA\Spreadsheets\SheetParser
     */
    protected function getSheetNames() {
        $this->data['sheetNames'] = $this->objPHPExcel->getSheetNames();
        
        return $this;
    }

    /**
     * 
     * @return \OCA\Spreadsheets\SheetParser
     */
    protected function iterateThroughWorksheets() {
        $s = 0;
        $keys = array_keys($this->data['sheets']);
        
        foreach ($this->objPHPExcel->getWorksheetIterator() as $worksheet) {
            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    if (!is_null($cell)) {
                        $rowData[] = $cell->getFormattedValue();
                    }
                }

                // Push into the related sheet array
                $this->data['sheets'][$keys[$s]][] = $rowData;
            }
            $s++;
        }
        
        return $this;
    }
    
    /**
     * 
     * @return \OCA\Spreadsheets\SheetParser
     */
    protected function handleMetaData() {
        $properties = $this->getFileProperties();

        $this->data['metaData'] = [
            'company' => $properties->getCompany(),
            'author' => $properties->getCreator(),
            'last_modified_by' => $properties->getLastModifiedBy(),
            'created_at' => $properties->getCreated(),
            'modified_at' => $properties->getModified(),
            'title' => $properties->getTitle(),
            'description' => $properties->getDescription(),
            'keywords' => $properties->getKeywords(),
            'filename' => $this->filename,
            'full_path' => $this->inputFilePath
        ];

        return $this;
    }
    
    /**
     * 
     * @return object
     */
    protected function getFileProperties() {
        return $this->objPHPExcel->getProperties();
    }

}
