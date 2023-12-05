<?php
namespace MarcXmlExport\Service\Form;

use MarcXmlExport\Form\ExportForm;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ExportFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $mappingManager = $services->get('MarcXmlExport\Manager');

        $form = new ExportForm(null, $options);
        $form->setMappingManager($mappingManager);

        return $form;
    }
}
