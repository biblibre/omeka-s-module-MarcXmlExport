<?php
namespace MarcXmlExport\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class ExportRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLd()
    {
        return [
            'created' => $this->getDateTime($this->created()),
            'name' => $this->name(),
            'query_params' => $this->queryParams(),
            'resource_type' => $this->resourceType(),
            'class_mapping' => $this->classMapping(),
            'file_path' => $this->filePath(),
        ];
    }

    public function getJsonLdType()
    {
        return 'o:marc_xml_export_exports';
    }

    public function created()
    {
        return $this->resource->getCreated();
    }

    public function name()
    {
        return $this->resource->getName();
    }

    public function queryParams()
    {
        return $this->resource->getQueryParams();
    }

    public function resourceType()
    {
        return $this->resource->getResourceType();
    }

    public function classMapping()
    {
        return $this->resource->getClassMapping();
    }

    public function filePath()
    {
        return $this->resource->getFilePath();
    }

    public function job()
    {
        return $this->getAdapter('jobs')
            ->getRepresentation($this->resource->getJob());
    }
}
