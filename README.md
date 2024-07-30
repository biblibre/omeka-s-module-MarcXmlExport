# MarcXml Export

This module produces and exports files in marcxml format from selected resources according to a defined mapping.

The complete documentation of MarcXmlExport can be found [here](https://biblibre.github.io/omeka-s-module-MarcXmlExport).

## Rationale

This module export a set of selected resources in a marcxml file which can be easily downloadable after.

## Requirements

* Omeka S >= 3.1.0

* The export path is currently hard-coded, so it is necessary to create a directory here : 

```bash
mkdir OMEKAS_DIR\files\Marc_XML_Export
```

## Quick start

1. [Add the module to Omeka S](https://omeka.org/s/docs/user-manual/modules/#adding-modules-to-omeka-s)
2. Login to the admin interface, and use it.

## Features

Actually this module includes an '_Unimarc_' mapping and a specific one for _Bokeh_ exchange but you can also add your own custom mapping by adding on `module.config.php`:

```php
'marcxmlexport_mapping' => [
            'factories' => [
                'your_mapping' => Path\To\Your\Mapping::class,
            ],
],
```

Then, choose the type of export (type of resource to export + mapping to use) and it will be launched the background task. 

You can download your exports from the _Past Exports_ page via the download icon link.

A script to clean up the files stored in ```/files/Marc_XML_Export/``` can be launched from the command line by specifying a 'date' argument corresponding to the deadline for which you want to keep the exports.

```bash
 ~/tools/omeka-s/job-start --user-id X --job-class 'MarcXmlExport\JobRemoveFilesJob' --job-args '{"date": "<YEAR>-<MONTH>-<DAY>"}' 
 ```

exports prior to this date will be deleted from the MarcXmlExport module table and the files in the ```/files/Marc_XML_Export/``` directory will also be deleted.

## How to contribute

You can contribute to this module by adding issues directly [here](https://github.com/biblibre/omeka-s-module-MarcXmlExport/issues).

## Contributors / Sponsors

Contributors:
* [ThibaudGLT](https://github.com/ThibaudGLT)

## Licence

MarcXmlExport is distributed under the GNU General Public License, version 3. The full text of this license is given in the LICENSE file.

Created by [BibLibre](https://www.biblibre.com).
