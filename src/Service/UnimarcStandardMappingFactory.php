<?php

namespace MarcXmlExport\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use MarcXmlExport\MappingClass\Unimarc\UnimarcStandard;
use Omeka\Module\Manager as ModuleManager;

class UnimarcStandardMappingFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $logger = $services->get('Omeka\Logger');
        $itemSetsTreeModule = $services->get('ModuleManager')->getModule("ItemSetsTree");
        $itemSetsTreeModuleIsActive = $itemSetsTreeModule && $itemSetsTreeModule->getState() === ModuleManager::STATE_ACTIVE;

        if ($itemSetsTreeModuleIsActive) {
            $itemSetsTreeService = $services->get('ItemSetsTree');
            return new UnimarcStandard($logger, $itemSetsTreeService);
        } else {
            return new UnimarcStandard($logger, null);
        }
    }
}
