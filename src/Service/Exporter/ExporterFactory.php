<?php
namespace MarcXmlExport\Service\Exporter;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use MarcXmlExport\Exporter\Exporter;

class ExporterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $api = $serviceLocator->get('Omeka\ApiManager');
        $mappingManager = $serviceLocator->get('MarcXmlExport\Manager');

        return new Exporter($api, $mappingManager);
    }
}
