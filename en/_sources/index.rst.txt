Introduction
============

How does it work ?
------------------

This module produces and exports files in marcxml format from selected resources according to a defined mapping.

Requirements
------------

The export path is currently hard-coded, so it is necessary to create a directory:

.. code-block:: bash

   mkdir OMEKAS_DIR\files\Marc_XML_Export

Where is the configuration
--------------------------

Actually `Unimarc` mapping is include in this module but you can add your own custom mapping on `module.config.php` file. (Also see :doc:`configuration`)
It will then be accessible when the export is created under "Select class mapping" dropdown

A specific mapping purposal is include too for `Bokeh` app exchange.

.. toctree::
   :maxdepth: 2
   :caption: Contents

   configuration
   features
   tutorials