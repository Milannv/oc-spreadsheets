<?php

namespace OCA\Spreadsheets;

use OCA\Spreadsheets\AuthorStorage;

/**
 * ownCloud - Spreadsheets App
 *
 * @author Milann Veldink
 * @copyright 2015 Milann Veldink <info@milannveldink.com>
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either 
 * version 3 of the License, or any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *  
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
class Storage {
    
    /**
     * 
     * @return type
     */
    public static function getSpreadsheets() {
        $list = array_filter(
                self::searchSpreadsheets(), function($item) {
            //filter Deleted
            if (strpos($item['path'], '_trashbin') === 0) {
                return false;
            }
            return true;
        }
        );
        
        return $list;
    }
    
    /**
     * 
     * @return array
     */
    protected static function searchSpreadsheets() {
        $sheets = [];

        foreach (self::getSupportedMimetypes() as $mime) {
            $sheets = array_merge($sheets, \OCP\Files::searchByMime($mime));
        }
        
        return $sheets;
    }

    /**
     * 
     * @return type
     */
    private static function getSupportedMimeTypes() {
        return [
            'application/vnd.ms-excel', // .xls
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'application/vnd.oasis.opendocument.spreadsheet' // .ods
        ];
    }

}
