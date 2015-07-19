<?php
script('spreadsheets', 'list');
script('spreadsheets', 'spreadsheet');
script('spreadsheets', 'main');
style('spreadsheets', 'style');
style('files', 'files');

// Handsontable
script('spreadsheets', 'handsontable/dist/handsontable_full_min');
style('spreadsheets', 'handsontable/handsontable');

// ruleJS + Handsontable formulae
script('spreadsheets', 'handsontable-formula/handsontable_formula');
style('spreadsheets', 'handsontable-formula/handsontable_formula');
script('spreadsheets', 'ruleJS/rulejs_no_moment');
?>

<div id="app">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('part.navigation')); ?>
        <?php //print_unescaped($this->inc('part.settings')); ?>
    </div>

    <div id="app-content">
        <div id="emptycontent" class="hidden">
            <div class="icon-folder"></div>
            <h2><?php p($l->t('No spreadsheets available')); ?></h2>
        </div>

        <div id="loadingcontent">
            <div class="icon-folder"></div>
            <h2><?php p($l->t('Loading files')); ?></h2>
        </div>

        <!-- Files table -->
        <table id="filestable" data-allow-public-upload="<?php p($_['publicUploadEnabled']) ?>" data-preview-x="36" data-preview-y="36">
            <thead>
                <tr>
                    <th id='headerName' class="colum-icon">
                        
                    </th>
                    <th id='headerName' class="column-name">
                    <div id="headerName-container">
                        <a class="name sort columntitle" data-sort="name"><span><?php p($l->t('Name')); ?></span>
                            <span class="sort-indicator"></span>
                        </a>
                    </div>
                </th>
                <th id="headerSize" class="column-size">
                    <a class="size sort columntitle" data-sort="size"><span><?php p($l->t('Size')); ?></span>
                        <span class="sort-indicator"></span>
                    </a>
                </th>
                <th id="headerDate" class="column-mtime">
                    <a id="modified" class="columntitle" data-sort="mtime"><span><?php p($l->t('Modified')); ?></span><span class="sort-indicator"></span></a>
                </th>
            </tr>
            </thead>
            <tbody id="fileList">
                <tr data-id="-1" data-file-id data-action-url class="hidden">
                    <td data-spec="thumbnail">
                        <div class="thumbnail" data-thumbnail-width="32"></div>
                    </td>
                    <td class="filename"></td>
                    <td class="filesize"></td>
                    <td class="date">
                        <span></span>
                    </td>
                </tr>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
        <!-- Editor -->
        <div id="spreadsheet-container" class="hidden" data-file-name>
            <div class="editor-top-container">
                <ul class="spreadsheet-edit-navigation left">
                    <li data-button-action="save-file" >
                        <?php p($l->t('Save changes')) ?>
                    </li>
                    <li data-button-action="quit">
                        <?php p($l->t('Quit')) ?>
                    </li>
                </ul>
                <div class="pagination right">
                    <span>Sheets:</span>
                </div>
            </div>
            <div style="clear: both"></div>
            <div id="spreadsheet-editor"></div>
        </div>
    </div>
</div>
