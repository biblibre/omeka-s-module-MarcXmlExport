Configuration
=============

Add your custom mapping
-----------------------

To add your own custom mapping add it on `module.config.php` file like:

.. code-block:: php

    'marcxmlexport_mapping' => [
        'factories' => [
            'your_mapping' => Path\To\Your\Mapping::class,
        ],
    ];

Cron clean-up
-------------
.. note::
    Need script from BibLibre public repository_.

.. _repository: https://git.biblibre.com/biblibre/tools/src/branch/master/omeka-s/job-start

A script to clean up the files stored in `~/files/Marc_XML_Export/` can be launched from the command line by specifying a 'date' argument corresponding to the deadline for which you want to keep the exports.

.. code-block:: bash

    ~/tools/omeka-s/job-start --user-id X --job-class 'MarcXmlExport\JobRemoveFilesJob' --job-args '{"date": "<YEAR>-<MONTH>-<DAY>"}'