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

use Exception;
use OCA\Spreadsheets\AbstractWriter;

class SheetWriter extends AbstractWriter {

    /**
     * 
     * @param string $path
     */
    public function setExportPath($path) {
        $this->exportPath = $path;
    }

    /**
     * 
     * @return Exception
     * @throws Exception
     */
    public function save() {
        if (!$this->exportPath) {
            throw new Exception('No export path specified');
        }
        
        if (!$this->isSupportedWriteFormat()) {
            // We save as .xls
            $this->setExportFormat('Excel5');
            
            $filename = pathinfo($this->contents['metaData']['full_path'], PATHINFO_FILENAME);
            $path = pathinfo($this->contents['metaData']['full_path'], PATHINFO_DIRNAME);
            
            $this->exportPath = $path . DIRECTORY_SEPARATOR . $filename . '.xls';
        }
        
        try {
            $this->generate();
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        try {
            $this->objWriter->save($this->exportPath);
        } catch (Exception $ex) {
            return $ex;
        }
    }

}
