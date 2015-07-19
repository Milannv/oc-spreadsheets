# About

This ownCloud 8.x spreadsheets plugin was mainly part of a research thesis for the Univeristy of Applied Sciences in Vorarlberg, Austria. As a result the app lacks a lot of handy features

Main motivation for developing this spreadsheet app for ownCloud was the following thread on Github: [App spec: spreadsheet editor #126]
  
# Development
At the moment, I don't have time and intentions to continue development of this package. If you feel like you are missing functionality, or more importantly, came across a bug, feel free to fix it! For an indication of open issues, look at the ToDo list below.

### Version
0.1 Initial alpha release 

### Tech
This app makes use of other open source libraries to parse and display the results. Thanks to them!
* [PHPExcel] A pure PHP library for reading and writing spreadsheet files
* [handsontable] an Excel-like data grid / spreadsheet for HTML & JavaScript.
* [handsontable-rulejs] Javascript library to parse excel formulas

### Installation
Simply go to your ownCloud installation and search for "spreadsheets" in the plugin section

### Todo's

* Write Tests
* Add support for saving ODF files
* Add support for downloading files
* Add support for creating new files
* Add support to modify metadata
* Add support to alter worksheets (e.g. renaming, deleting and adding worksheets)
* Add support for file uploading on the app index 
* Add support for sheet collaboration
* And a lot more... ;-)

### Known issue(s)
* File date is displayed incorrectly


License
----

[GNU Affero General Public License version 3.0]

[PHPExcel]:https://github.com/PHPOffice/PHPExcel
[handsontable]:https://github.com/handsontable/handsontable
[handsontable-rulejs]:https://github.com/handsontable/handsontable-ruleJS
[GNU Affero General Public License version 3.0]:http://www.gnu.org/licenses/agpl-3.0.html
[App spec: spreadsheet editor #126]:https://github.com/owncloud/apps/issues/126


