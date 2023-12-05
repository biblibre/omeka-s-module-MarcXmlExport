<?php

namespace MarcXmlExport\MappingClass;

use Omeka\ServiceManager\AbstractPluginManager;

class Manager extends AbstractPluginManager
{
    protected $instanceOf = MappingClassInterface::class;
}
