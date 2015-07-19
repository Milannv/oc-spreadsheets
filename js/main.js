/**
 * ownCloud - spreadsheets
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Milann Veldink <info@milannveldink.com>
 * @copyright Milann Veldink 2015
 */
var Spreadsheets = Spreadsheets || {};
var spreadsheetEditor = new spreadsheetEditor();
var fileList = new fileList();

(function (window, $, Spreadsheets, spreadsheetEditor, fileList) {
    'use strict';
    
    fileList.loadFiles();

    $(document).ready(function () {
        $('body').on('click', 'tbody#fileList tr', function () {
            // Retrieve file name
            var filename = $(this).attr('data-file-name') || false;

            if (!filename)
            {

                fileList.selectedFilename = null;
                return;
            }

            fileList.selectedFilename = filename;

            location.replace('#!/edit/' + filename);

            // Request actual file
            fileList.requestFile();
        });

        $('body').on('click', 'a[data-link-type="sheet-selection"]', function (e) {
            e.preventDefault();
            var selectedSheet;

            if (!spreadsheetEditor.handsontableInstance)
            {
                return;
            }

            selectedSheet = $(this).attr('href').replace('#', '') || false;

            if (!selectedSheet)
            {
                return;
            }

            spreadsheetEditor.currentSheet = selectedSheet;
            spreadsheetEditor._setActiveWorksheetButton();

            var hotInstance = spreadsheetEditor.handsontableInstance.handsontable('getInstance');
            hotInstance.loadData(spreadsheetEditor._getOriginalSheetData());
        });

        $('li[data-button-action="quit"]').click(function () {
            spreadsheetEditor._exitEditor();
        });

        $('li[data-button-action="save-file"]').click(function () {
            var button = $(this);
            
            if($(button).hasClass('processing')) {
                return;
            }
            
            var url = OC.generateUrl('apps/spreadsheets/save');
            // If we use the hotinstance.getData() method, only the current 
            // sheet data will be returned
            var contents = spreadsheetEditor._getAllModifiedSheetData();
            var data = {
                metaData: contents['metaData'],
                sheetNames: contents['sheetNames'],
                sheets: contents['sheets']
            };
            
            $(button).addClass('processing').text('Saving ...');
            
            $.ajax({
                type: "POST",
                url: url,
                cache: false,
                data: data,
                async: true,
                success: function (data)
                {
                    if(data.success) {
                        spreadsheetEditor.modificationsSaved = true;
                        spreadsheetEditor.isModified = false;
                    }
                    
                    $(button).text('Saved')
                            .removeClass('processing')
                            .delay(1500).queue(function () {
                                $(this).text('Save file');
                            });
                },
                error: function (xhr, status, error)
                {
                    // ToDo: add adequate error messaging
                    console.log(xhr, status, error);
                    $(button).removeClass('processing').text('Save file');
                }
            });
        });
    });

    $(window).bind('beforeunload', function () {
        if (spreadsheetEditor.isModified && !spreadsheetEditor.modificationsSaved)
        {
            return "You have unsaved modifications";
        }
    });

})(window, jQuery, Spreadsheets, spreadsheetEditor, fileList);