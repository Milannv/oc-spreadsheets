<?php
script('spreadsheets', 'spreadsheet');
style('spreadsheets', 'style');
style('files', 'files');
?>

<div id="app">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('part.navigation')); ?>
        <?php print_unescaped($this->inc('part.settings')); ?>
    </div>
</div>