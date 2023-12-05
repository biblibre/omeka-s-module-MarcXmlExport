<?php
namespace MarcXmlExport\MappingClass;

use DOMDocument;

interface MappingClassInterface
{
    public function getXmlFile($resources):DOMDocument;

    public function getLabel();
}
