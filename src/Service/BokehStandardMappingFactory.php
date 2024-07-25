<?php

namespace MarcXmlExport\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use MarcXmlExport\MappingClass\Bokeh\BokehStandard;
use Omeka\Module\Manager as ModuleManager;

class BokehStandardMappingFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $logger = $services->get('Omeka\Logger');
        $api = $services->get('Omeka\ApiManager');

        $moduleManager = $services->get('ModuleManager');
        $itemSetsTreeModule = $moduleManager->getModule("ItemSetsTree");
        $itemSetsTreeModuleIsActive = $itemSetsTreeModule && $itemSetsTreeModule->getState() === ModuleManager::STATE_ACTIVE;
        
        if ($itemSetsTreeModuleIsActive) {
            $itemSetsTreeService = $services->get('ItemSetsTree');
            return new BokehStandard($logger, $itemSetsTreeService, $api, $moduleManager);
        } else {
            return new BokehStandard($logger, null, $api, $moduleManager);
        }
    }

}
