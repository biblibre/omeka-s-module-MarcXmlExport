<?php

namespace MarcXmlExport\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use MarcXmlExport\MappingClass\Bokeh\BokehStandard;

class BokehStandardMappingFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $logger = $services->get('Omeka\Logger');
        $api = $services->get('Omeka\ApiManager');
        $moduleManager = $services->get('Omeka\ModuleManager');

        $itemSetsTreeModule = $moduleManager->getModule("ItemSetsTree");
        $itemSetsTreeModuleIsActive = $itemSetsTreeModule && $itemSetsTreeModule->getState() === 'active';

        $groupModule = $moduleManager->getModule("Group");
        $groupModuleIsActive = $groupModule && $groupModule->getState() === 'active';

        if ($itemSetsTreeModuleIsActive) {
            $itemSetsTreeService = $services->get('ItemSetsTree');
            return new BokehStandard($logger, $itemSetsTreeService, $api, $groupModuleIsActive);
        } else {
            return new BokehStandard($logger, null, $api, $groupModuleIsActive);
        }
    }
}
