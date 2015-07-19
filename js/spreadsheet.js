/**
 * ownCloud - spreadsheets
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Milann Veldink <info@milannveldink.com>
 * @copyright Milann Veldink 2015
 */
var spreadsheetEditor = function () {
    this.spreadsheetEditorContainer = '#spreadsheet-container';
    this.spreadsheetEditor = '#spreadsheet-editor';
    this.handsontableInstance = null;
    this.currentSheet = null;
    this.originalWorkbookData = {};
    this.modifiedWorkbookData = {};
    this.isModified = false;
    this.modificationsSaved = false;
};

spreadsheetEditor.prototype.loadEditorView = function ()
{
    this._loadEditor();
};
spreadsheetEditor.prototype._getCurrentSheet = function ()
{
    return this.currentSheet || this.originalWorkbookData.sheetNames[0];
};
spreadsheetEditor.prototype._getOriginalSheetData = function ()
{
    var sheet = this._getCurrentSheet();

    if (typeof this.originalWorkbookData['sheets'][sheet] !== undefined)
    {
        return this.originalWorkbookData['sheets'][sheet];
    }
    else
    {
        return [];
    }
};
spreadsheetEditor.prototype._getAllModifiedSheetData = function ()
{
    return this.modifiedWorkbookData;
};
spreadsheetEditor.prototype._getMetaData = function ()
{
    return this.originalWorkbookData['metaData'] || {};
};
spreadsheetEditor.prototype._loadEditor = function ()
{
    fileList.hideFilesTable();
    this._createSheetLinks();

    $(this.spreadsheetEditorContainer).fadeIn();
    this.currentSheet = this._getCurrentSheet();
    this._initHandsonTable();
    this._showMetaData();
    this._setActiveWorksheetButton();
};
spreadsheetEditor.prototype._hideEditor = function ()
{
    $(this.spreadsheetEditorContainer).hide();
};
spreadsheetEditor.prototype._initHandsonTable = function ()
{
    if (this.handsontableInstance)
    {
        return;
    }

    this.handsontableInstance = $(this.spreadsheetEditor).handsontable({
        data: this._getOriginalSheetData(),
        contextMenu: true,
        rowHeaders: true,
        colHeaders: true,
        minSpareRows: 1,
        minSpareCols: 1,
        manualColumnResize: true,
        manualRowResize: true,
        //manualColumnMove: true, #ToDo WorkbookData should be altered accordingly
        //manualRowMove: true, #ToDo WorkbookData should be altered accordingly
        stretchH: "all",
        currentRowClassName: 'currentRow',
        currentColClassName: 'currentCol',
//            columnSorting: {
//                column: 2
//            },
        sortIndicator: true,
        afterChange: function (changes, source) {
            if (source === 'loadData')
            {
                return;
            }

            spreadsheetEditor._modifyWorkbookDataColumnChanges(changes);
        },
//            afterColumnSort: function(column, order)
//            {
//                
//            },
//            afterColumnMove: function(oldIndex, newIndex)
//            {
//                
//            },
        formulas: true
    });
};
spreadsheetEditor.prototype._modifyWorkbookDataColumnChanges = function (changes)
{
    if (!this.isModified)
    {
        this.isModified = true;
    }
    for (var c in changes)
    {
        var change = changes[c];

        // From the documentation: changes is a 2D array containing information about each of the edited cells [ [row, prop, oldVal, newVal], ... ]
        var row = change[0];
        var prop = change[1];
        var newValue = change[3];

        if (typeof this.modifiedWorkbookData['sheets'][this._getCurrentSheet()] !== 'undefined')
        {
            this.modifiedWorkbookData['sheets'][this._getCurrentSheet()][row][prop] = newValue;
        }
    }
};
spreadsheetEditor.prototype._createSheetLinks = function ()
{
    var $target = $('.pagination');
    var sheets = this.originalWorkbookData['sheetNames'];

    if ($target.find('a').length > 0) {
        $target.find('a').each(function (index, el) {
            el.remove();
        });
    }

    for (var index in sheets) {
        var sheet = sheets[index];

        $target.append('<a href="#' + sheet + '" data-link-type="sheet-selection">' + sheet + '</a>');
    }
};
spreadsheetEditor.prototype._showMetaData = function ()
{
    var md = this._getMetaData();

    for (var key in md)
    {
        if (key === 'created_at' || key === 'modified_at')
        {
            // Convert into human readable date
            md[key] = formatDate(md[key]);
        }

        if (typeof md[key] !== undefined && md[key].length <= 0)
        {
            $('li[data-list-type="' + key + '"]').hide();
        }
        else
        {
            $('li[data-list-type="' + key + '"]').show().find('span').text(md[key]);
        }
    }

    $('ul[data-list-type="metadata-list"]').show();

};
spreadsheetEditor.prototype._hideAndClearMetaData = function ()
{
    $('li[data-list-type]').each(function (index, element) {
        $(element).find('span').text('');
    });

    $('ul[data-list-type="metadata-list"]').hide();
};
spreadsheetEditor.prototype._exitEditor = function ()
{
    this.originalWorkbookData = {};
    this.modifiedWorkbookData = {};
    this.isModified = false;
    this.modificationsSaved = true;
    this.currentSheet = null;
    this.handsontableInstance = null;

    this._hideAndClearMetaData();
    this._hideEditor();
    fileList.showFilesTable();
    
    // Remove window hash
    history.replaceState({}, document.title, ".");
};
spreadsheetEditor.prototype._setActiveWorksheetButton = function ()
{
    // Delete current active class
    $('div.pagination a.active').removeClass('active');

    $('div.pagination a[href="#' + this.currentSheet + '"]').addClass('active');
};