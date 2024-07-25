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
        try {
            $itemSetsTreeService = $services->get('ItemSetsTree');
        } catch (\Exception $e) {
            throw $e;
        }
        return new BokehStandard($logger, $itemSetsTreeService, $api);
    }
}
