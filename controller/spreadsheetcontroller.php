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

namespace OCA\Spreadsheets\Controller;

use \OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use \OCA\Spreadsheets\Storage;
use OCA\Spreadsheets\SheetParser;
use OCA\Spreadsheets\SheetWriter;

class SpreadsheetController extends Controller {

    private $userId;

    public function __construct($AppName, IRequest $request, $UserId) {
        parent::__construct($AppName, $request);
        $this->userId = $UserId;
    }

    /**
     * CAUTION: the @Stuff turns off security checks; for this page no admin is
     *          required and no CSRF check. If you don't know what CSRF is, read
     *          it up in the docs or you might create a security hole. This is
     *          basically the only required method to add this exemption, don't
     *          add it to any other method if you don't exactly know what it does
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        $params = [
            'user' => $this->userId,
        ];

        return new TemplateResponse('spreadsheets', 'main', $params);  // templates/main.php
    }

    /**
     * 
     * @return array
     */
    public function getFilesList() {
        $spreadsheets = Storage::getSpreadsheets();
        $files = [];
        foreach ($spreadsheets as $key => $document) {
            if (is_object($document)) {
                $files[] = $document->getData();
            } else {
                $files[$key] = $document;
            }
            $files[$key]['icon'] = preg_replace(
                    '/\.png$/', '.svg', 
                    \OC_Helper::mimetypeIcon($document['mimetype'])
                    );
        }

        usort($files, function($a, $b) {
            return @$b['mtime'] - @$a['mtime'];
        });
        
        return [
            'files' => $files,
        ];
    }
    
    /**
     * 
     * @param string $filename
     * @return DataResponse
     */
    public function getSheet($filename)
    {
        if(!\OC\Files\Filesystem::file_exists($filename))
        {
            return new DataResponse([
                'success' => false,
                'error' => 'File does not exist',
            ]);
        }

        $path = \OC\Files\Filesystem::getLocalFile($filename);
        
        $parser = new SheetParser($path, $filename);
        
        try {
            $response = $parser->readFile();
        } catch (Exception $ex) {
            return new DataResponse([
                'success' => false,
                'error' => $ex->getMessage(),
            ]);
        }
        
        return new DataResponse([
            $response->getDataResponse()
        ]);
    }
    
    /**
     * Save a spreadsheet
     * 
     * @return array
     */
    public function save()
    {
        $requestParams = $this->request->getParams();
        
        try {
            $save = new SheetWriter($requestParams);
        } catch (Exception $ex) {
            return [
                'success' => false,
                'error' => $ex->getMessage(),
            ];
        }
        $save->setExportPath($requestParams['metaData']['full_path']);
        $save->setSheetNames($requestParams['sheetNames']);
        
        try {
            $save->save();
        } catch (Exception $ex) {
            return [
                'success' => false,
                'error' => $ex->getMessage(),
            ];
        }
        
        return [
            'success' => true,
            'data' => $requestParams,
        ];
    }
    
}
