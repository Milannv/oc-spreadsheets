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

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Spreadsheets\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	   ['name' => 'spreadsheet#index', 'url' => '/', 'verb' => 'GET'],
           ['name' => 'spreadsheet#get_files_list', 'url' => '/ajax/load/files', 'verb' => 'GET'],
           ['name' => 'spreadsheet#get_sheet', 'url' => '/ajax/load/sheet/{filename}', 'verb' => 'GET'],
           ['name' => 'spreadsheet#save', 'url' => '/save', 'verb' => 'POST'],
    ]
];