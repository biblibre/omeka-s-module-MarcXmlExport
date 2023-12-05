<?php

namespace MarcXmlExport\Exporter;

class Exporter
{
    protected $api;
    protected $mappingManager;

    public function __construct($api, $mappingManager)
    {
        $this->api = $api;
        $this->mappingManager = $mappingManager;
    }

    public function exportQuery($resourceType, $query, $mappingName)
    {
        if (!isset($query)) {
            $queryArray = $_GET;
        } else {
            parse_str($query, $queryArray);
        }

        $mapping = $this->mappingManager->get($mappingName);
        $resources = $this->api->search($resourceType, $queryArray)->getContent();
        $xmlOutput = $mapping->getXmlFile($resources);

        return $xmlOutput;
    }
}
