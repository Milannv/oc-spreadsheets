/**
 * ownCloud - spreadsheets
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Milann Veldink <info@milannveldink.com>
 * @copyright Milann Veldink 2015
 */
var fileList = function () {
    this.selectedFilename = null;
};

fileList.prototype.loadFiles = function () {
    var url = OC.generateUrl('apps/spreadsheets/ajax/load/files');

    $.ajax({
        type: "GET",
        url: url,
        cache: false,
        dataType: "json",
        async: true,
        success: function (data)
        {
            fileList._handleFilesListSuccess(data);
        }
    });
};

fileList.prototype.requestFile = function ()
{
    var url = OC.generateUrl('apps/spreadsheets/ajax/load/sheet/{filename}', {filename: this.selectedFilename});

    $.ajax({
        type: "GET",
        url: url,
        cache: false,
        dataType: "json",
        async: true,
        success: function (data)
        {
            if (data.length === 1)
            {
                spreadsheetEditor.originalWorkbookData = data[0];
                spreadsheetEditor.loadEditorView();

                // Set modified workbookdata source with current 
                spreadsheetEditor.modifiedWorkbookData = spreadsheetEditor.originalWorkbookData;
            }
        }
    });
};

fileList.prototype._handleFilesListSuccess = function (data)
{
    if (typeof data.files === 'undefined' || data.files.length <= 0)
    {
        this.hideLoadingContentContainer();
        this.showEmptyContentContainer();

        return;
    }

    this._prepareFilesList(data.files);
    this.hideLoadingContentContainer();
    this.showFilesTable();
};
fileList.prototype._prepareFilesList = function (files)
{
    for (var i in files)
    {
        var item = files[i];
        this._createFileRow(item);
    }
};
fileList.prototype._createFileRow = function (data)
{
    // Clone skeleton row
    var $row = $('tbody#fileList').find('tr[data-id="-1"]').clone(true);
    var thumbWidth = $row.find('td[data-spec="thumbnail"]').find('div.thumbnail').attr('data-thumbnail-width');
    var formatted;
    var mtime;
    var text;
    
    // Prepend row data
    $row.find('td[data-spec="thumbnail"] div.thumbnail').css({
        'height': thumbWidth + 'px',
        'width': thumbWidth + 'px',
        'background-image': 'url(' + data.icon + ')',
        'background-size': thumbWidth + 'px'
    });

    var humanFilesize = humanFileSize(parseInt(data.size, 10), true);
    mtime = data.mtime;
    
    if (mtime > 0) {
            formatted = formatDate(mtime);
            text = OC.Util.relativeModifiedDate(mtime);
    } else {
            formatted = t('files', 'Unable to determine date');
            text = '?';
    }

    $row.attr('data-id', ++this.fileCounter);
    $row.attr('data-file-id', data.fileid);
    $row.attr('data-file-name', data.name);
    $row.find('td.filename').text(data.name);
    $row.find('td.filesize').text(humanFilesize);
    $row.find('td.date span').text(text);

    // Remove hidden class
    $row.removeClass('hidden');

    this._insertItemRowIntoFilesList($row);
};
fileList.prototype._insertItemRowIntoFilesList = function (row)
{
    $('tbody#fileList').append(row);
};

fileList.prototype.showFilesTable = function ()
{
    $('table#filestable').fadeIn();
};
fileList.prototype.hideFilesTable = function ()
{
    $('table#filestable').hide();
};
fileList.prototype.hideLoadingContentContainer = function ()
{
    $('#loadingcontent').fadeOut();
};
fileList.prototype.showEmptyContentContainer = function ()
{
    $('#emptycontent').fadeIn();
};