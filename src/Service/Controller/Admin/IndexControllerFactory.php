<?php

namespace MarcXmlExport\Service\Controller\Admin;

use MarcXmlExport\Controller\Admin\IndexController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $indexController = new IndexController();

        return $indexController;
    }
}
