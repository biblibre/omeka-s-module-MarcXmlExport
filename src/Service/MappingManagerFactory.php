<?php
namespace MarcXmlExport\Service;

use MarcXmlExport\MappingClass\Manager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Omeka\Service\Exception;

class MappingManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $config = $serviceLocator->get('Config');
        if (!isset($config['marcxmlexport_mapping'])) {
            throw new Exception\ConfigException('Missing marcxmlexport_mapping configuration');
        }

        return new Manager($serviceLocator, $config['marcxmlexport_mapping']);
    }
}
