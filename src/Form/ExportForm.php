<?php

namespace MarcXmlExport\Form;

use Laminas\Form\Form;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;

class ExportForm extends Form
{
    protected $mappingManager;

    public function init()
    {
        $this->setAttribute('action', 'save');

        $this->add([
            'name' => 'export_name',
            'type' => 'Text',
            'options' => [
                'label' => 'Export name', //@translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'class_mapping',
            'type' => Select::class,
            'options' => [
                'label' => 'Select class mapping', //@translate
                'info' => 'Select class to process mapping', //@translate
                'value_options' => $this->getMappingOptions(),
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'resource_type',
            'type' => Radio::class,
            'options' => [
                'label' => 'Select resource type', //@translate
                'value_options' => [
                    'item_sets' => 'Item sets', //@translate
                    'items' => 'Items', //@translate
                    'media' => 'Medias', //@translate
                ],
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'resource_visibility',
            'type' => Radio::class,
            'options' => [
                'label' => 'Select resources visibility', //@translate
                'value_options' => [
                    'all' => 'All resources', //@translate
                    'public' => 'Only public resources', //@translate
                    'private' => 'Only private resources', //@translate
                ],
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'query_params',
            'type' => 'Omeka\Form\Element\Query',
            'options' => [
                'label' => 'Specify resources in query', //@translate
                'info' => 'Build or type a SQL query to select resources', //@translate
            ],
        ]);
    }

    public function setMappingManager($mappingManager)
    {
        $this->mappingManager = $mappingManager;
    }

    public function getMappingManager()
    {
        return $this->mappingManager;
    }

    protected function getMappingOptions()
    {
        $mappingManager = $this->getMappingManager();
        $mappingNames = $mappingManager->getRegisteredNames();

        $options = [];

        foreach ($mappingNames as $name) {
            $mapping = $mappingManager->get($name);
            $options[$name] = $mapping->getLabel();
        }

        return $options;
    }
}
