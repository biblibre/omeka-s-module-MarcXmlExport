# MarcXml Export for Omeka S

This module add possibility to export datas according to Marc XML format.

## Requirements

The export path is currently hard-coded, so it is necessary to create a directory here : 

```bash
mkdir OMEKAS_DIR\files\Marc_XML_Export
```

## Description

The mapping is completely hard-coded and cannot be configured by the interface.

But this module can receive different mapping by adding another modules and using the config to use it.

Like this for example :

```php
'marcxmlexport_mapping' => [
            'factories' => [
                'your_mapping' => Path\To\Your\Mapping::class,
            ],
],
```

The mapping is completely hard-coded and cannot be configured by the interface.

From the moment you choose the type of export (type of resource to export + mapping to use) you only have to launch the background task. 

You can then retrieve your files from the main page of the module via the download link.

A script to clean up the files stored in ```/files/Marc_XML_Export/``` can be launched from the command line by specifying a 'date' argument corresponding to the deadline for which you want to keep the exports.

Example, I want to keep the exports since 2023-01-01 so I launch the job via : 

```bash
 ~/tools/omeka-s/job-start --user-id X --job-class 'MarcXmlExport\JobRemoveFilesJob' --job-args '{"date": "2023-01-01"}' 
 ```

exports prior to this date will be deleted from the MarcXmlExport module table and the files in the ```/files/Marc_XML_Export/``` directory will also be deleted.

## Warning

Use it at your own risk.

It's always recommended to backup your files and your databases and to check your archives regularly so you can roll back if needed.

## License

This plugin is published under the GNU General Public License v3.0

## Copyright

Copyright BibLibre, 2015-2023
