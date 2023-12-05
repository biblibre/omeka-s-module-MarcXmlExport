<?php

namespace MarcXmlExport\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use MarcXmlExport\MappingClass\Unimarc\UnimarcStandard;

class UnimarcStandardMappingFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $logger = $services->get('Omeka\Logger');
        try {
            $itemSetsTreeService = $services->get('ItemSetsTree');
        } catch (\Exception $e) {
            throw $e;
        }
        return new UnimarcStandard($logger, $itemSetsTreeService);
    }
}
