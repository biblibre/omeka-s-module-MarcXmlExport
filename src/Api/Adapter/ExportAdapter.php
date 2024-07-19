<?php
namespace MarcXmlExport\Api\Adapter;

use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;
use MarcXmlExport\Api\Representation\ExportRepresentation;
use MarcXmlExport\Entity\MarcXmlExportExports;

class ExportAdapter extends AbstractEntityAdapter
{
    public function getResourceName()
    {
        return 'marc_xml_export_exports';
    }

    public function getRepresentationClass()
    {
        return ExportRepresentation::class;
    }

    public function getEntityClass()
    {
        return MarcXmlExportExports::class;
    }

    public function hydrate(
        Request $request,
        EntityInterface $entity,
        ErrorStore $errorStore
    ) {
        $data = $request->getContent();

        if (isset($data['o:job']['o:id'])) {
            $job = $this->getAdapter('jobs')->findEntity($data['o:job']['o:id']);
            $entity->setJob($job);
        }

        if (isset($data['created'])) {
            $entity->setCreated($data['created']);
        }

        if (isset($data['name'])) {
            $entity->setName($data['name']);
        }

        if (isset($data['query_params'])) {
            $entity->setQueryParams($data['query_params']);
        }

        if (isset($data['resource_type'])) {
            $entity->setResourceType($data['resource_type']);
        }

        if (isset($data['resource_visibility'])) {
            $entity->setResourceVisibility($data['resource_visibility']);
        }

        if (isset($data['class_mapping'])) {
            $entity->setClassMapping($data['class_mapping']);
        }

        if (isset($data['file_path'])) {
            $entity->setFilePath($data['file_path']);
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        $exportName = $entity->getName();

        if (!$this->isUnique($entity, ['name' => $exportName])) {
            $errorStore->addError('o-module-MarcXmlExport-export:name', sprintf(
                'The name %s is already taken.', // @translate
                $exportName
            ));
        }
    }
}
