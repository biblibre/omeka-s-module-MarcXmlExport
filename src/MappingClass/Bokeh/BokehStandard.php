<?php

namespace MarcXmlExport\MappingClass\Bokeh;

use DOMDocument;
use MarcXmlExport\MappingClass\Unimarc\UnimarcStandard;

class BokehStandard extends UnimarcStandard
{
    protected $logger;
    protected $api;
    protected $itemSetsTreeService;
    protected $groupModuleIsActive;
    protected $propertiesVisibility = 'only_public';

    public function __construct($logger, $itemSetsTreeService, $api, $groupModuleIsActive)
    {
        $this->logger = $logger;
        $this->api = $api;
        $this->itemSetsTreeService = $itemSetsTreeService;
        $this->groupModuleIsActive = $groupModuleIsActive;
    }

    public function getLabel()
    {
        return 'Bokeh Standard';
    }

    public function getXmlFile($resources) : DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $collection = $dom->createElementNS('http://www.loc.gov/MARC21/slim', 'xlms:collection');
        $collection->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $collection->setAttribute('xsi:schemaLocation', 'http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd');
        $dom->appendChild($collection);
        $this->setDom($dom);

        foreach ($resources as $resource) {
            $record = $dom->createElement('record');
            $collection->appendChild($record);

            $fieldsMapping = $this->getFieldsMapping($resource);
            $repeatableFieldsMapping = $this->getRepeatableFieldsMapping($resource);

            foreach ($fieldsMapping as $tag => $value) {
                $this->addElement($tag, $value, $record);
            }
            foreach ($repeatableFieldsMapping as $tag => $value) {
                $this->addRepeatableElement($tag, $value, $record);
            }
            if (isset($metadatasMapping)) {
                foreach ($metadatasMapping as $property => $mappingDatas) {
                    $this->addMetadataElement($resource, $property, $mappingDatas, $record);
                }
            }
        }
        header("Content-Type: text/xml");

        return $dom;
    }

    protected function getFieldsMapping($resource)
    {
        $resourceType = $resource->getResourceJsonLdType();
        $resourceTypeValues = [
            'o:ItemSet' => "collection",
            'o:Item' => "item",
            'o:Media' => "media",
        ];

        $fieldsMapping = [
            '001' => $resource->id(),
            '099' => [
                'c' => $resource->created()->format('Y-m-d'),
                'd' => $resource->modified()->format('Y-m-d'),
                'o' => $resource->isPublic(),
                'y' => $resource->owner() ? $resource->owner()->name() : null,
            ],
            '299' => [
                'a' => "OMEKAS",
                'b' => $resourceTypeValues[$resourceType],
            ],
            '956' => ['u' => $resource->thumbnailDisplayUrl('large')],
            '995' => [
                'a' => 'Bibliothèque numérique',
                'b' => 'Bibliothèque numérique',
                'f' => 'omekas-' . $resource->id(),
            ],
        ];

        $fieldsMapping = $this->addResourceTemplateMap($fieldsMapping, $resource);
        $fieldsMapping = $this->addResourceClassMap($fieldsMapping, $resource);

        if ($resourceType === 'o:Media') {
            $fieldsMapping['049'] = [
                'a' => $resource->item()->id(),
                'b' => "Item",
                'c' => $resource->displayTitle(),
            ];
        }
        return $fieldsMapping;
    }

    protected function getRepeatableFieldsMapping($resource)
    {
        $repeatableFieldsMapping = [];
        $repeatableFieldsMapping = $this->addItemSetsMap($repeatableFieldsMapping, $resource);
        $repeatableFieldsMapping = $this->addSitesMap($repeatableFieldsMapping, $resource);
        if ($this->groupModuleIsActive) {
            $repeatableFieldsMapping = $this->addGroupsMap($repeatableFieldsMapping, $resource);
        }

        return $repeatableFieldsMapping;
    }

    protected function addResourceTemplateMap($mapping, $resource)
    {
        if ($resource->resourceTemplate()) {
            $mapping['069'] = [
                'a' => $resource->resourceTemplate()->id(),
                'b' => $resource->resourceTemplate()->label(),
            ];
        }
        return $mapping;
    }
    protected function addResourceClassMap($mapping, $resource)
    {
        if ($resource->resourceClass()) {
            $mapping['079'] = [
                'a' => $resource->resourceClass()->id(),
                'b' => $resource->resourceClass()->label(),
            ];
        }
        return $mapping;
    }

    protected function addSitesMap($mapping, $resource)
    {
        $resourceType = $resource->getResourceJsonLdType();
        $sites = $resourceType === 'o:Media' ? $resource->item()->sites() : $resource->sites();
        if (isset($sites)) {
            foreach ($sites as $site) {
                $mapping['059'][] = [
                    'a' => $site->id(),
                    'b' => $site->title(),
                ];
            }
        }
        return $mapping;
    }

    protected function addGroupsMap($mapping, $resource)
    {
        $groups = $this->api->search('groups', ['resource_id' => $resource->id()])->getContent();
        if (isset($groups)) {
            foreach ($groups as $group) {
                $mapping['089'][] = [
                    'a' => $group->id(),
                    'b' => $group->name(),
                    'c' => $group->comment(),
                ];
            }
        }
        return $mapping;
    }

    protected function addItemSetsMap($mapping, $resource)
    {
        $resourceType = $resource->getResourceJsonLdType();

        if ($resourceType === 'o:ItemSet' && $this->itemSetsTreeService) {
            $ancestors = $this->itemSetsTreeService->getAncestors($resource);
            if (isset($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $mapping['049'][] = [
                        'a' => $ancestor->id(),
                        'b' => "Collection parent",
                        'c' => $ancestor->title(),
                    ];
                }
            }
        }
        if ($resourceType === 'o:Item') {
            if ($resource->itemSets()) {
                foreach ($resource->itemSets() as $itemSet) {
                    $mapping['049'][] = [
                        'a' => $itemSet->id(),
                        'b' => "Collection",
                        'c' => $itemSet->title(),
                    ];
                }
            }
        }
        return $mapping;
    }

    protected function addRepeatableElement($tag, $value, $parentNode)
    {
        $dom = $this->getDom();
        foreach ($value as $subfieldValues) {
            $field = $dom->createElement('datafield');
            $field->setAttribute('tag', $tag);
            foreach ($subfieldValues as $subfieldChildKey => $subfieldChildValue) {
                $subfield = $dom->createElement('subfield', $this->trimAndEscape($subfieldChildValue));
                $subfield->setAttribute('code', $subfieldChildKey);
                $field->appendChild($subfield);
            }
            $parentNode->appendChild($field);
        }
    }

    protected function processMapping($values, $mappingDatas, $parentNode)
    {
        $dom = $this->getDom();
        $repeatableField = $mappingDatas['repeatable'];
        $code = $mappingDatas['subfield'];
        $tag = $mappingDatas['tag'];
        $transformation = $mappingDatas['transformation'];

        if (! empty($values)) {
            $field = $this->getFieldByTag($tag, $parentNode);
            if (is_array($values)) {
                foreach ($values as $value) {
                    if ($this->isPropertyToExport($value)) {
                        if ($transformation) {
                            if ($transformation['type'] == 'prefix') {
                                $transformedValue = $transformation['value'] . $value;
                            }
                            if ($transformation['type'] == 'suffix') {
                                $transformedValue .= $transformation['value'];
                            }
                        }
                        $valueType = explode(':', $value->type())[0];
                        switch ($valueType) {
                            case 'resource':
                                $field = $this->addResourceValue($dom, $field, $value);
                                break;

                            case 'valuesuggest':
                                $field = $this->addValueSuggestValue($dom, $field, $value);
                                break;

                            case 'uri':
                                $field = $this->addUriValue($dom, $field, $value);
                                break;

                            default:
                                $value = isset($transformedValue) ? $transformedValue : $value;
                                $field = $this->addLiteralValue($dom, $field, $code, $value);
                                break;
                        }

                        if ($repeatableField && $field) {
                            $parentNode->appendChild($field);
                        }

                        if (!$repeatableField && $field) {
                            $parentNode->appendChild($field);
                        }
                    }
                }
            } else {
                $value = $values;
                if ($this->isPropertyToExport($value)) {
                    if ($transformation) {
                        if ($transformation['type'] == 'prefix') {
                            $transformedValue = $transformation['value'] . $value;
                        }
                        if ($transformation['type'] == 'suffix') {
                            $transformedValue .= $transformation['value'];
                        }
                    }
                    $valueType = explode(':', $value->type())[0];
                    switch ($valueType) {
                        case 'resource':
                            $field = $this->addResourceValue($dom, $field, $value);
                            break;

                        case 'valuesuggest':
                            $field = $this->addValueSuggestValue($dom, $field, $value);
                            break;

                        case 'uri':
                            $field = $this->addUriValue($dom, $field, $value);
                            break;

                        default:
                            $value = isset($transformedValue) ? $transformedValue : $value;
                            $field = $this->addLiteralValue($dom, $field, $code, $value);
                            break;
                    }

                    if ($repeatableField && $field) {
                        $parentNode->appendChild($field);
                    }

                    if (!$repeatableField && $field) {
                        $parentNode->appendChild($field);
                    }
                }
            }
        }
    }

    protected function addResourceValue($dom, $field, $value)
    {
        $resource = $value->valueResource();
        $resourceController = $resource->getControllerName();

        $subfield = $dom->createElement('subfield', $this->trimAndEscape($resource->displayTitle()));
        $subfield->setAttribute('code', 'a');
        $field->appendChild($subfield);

        switch ($resourceController) {
            case 'item-set':
                $subfieldValue = $resource->id();
                $code = '2';
                break;

            case 'item':
                $subfieldValue = $resource->id();
                $code = '1';
                break;

            case 'media':
                $subfieldValue = $resource->id();
                $code = '3';
                break;

            case 'taxonomy':
                $subfieldValue = $resource->code();
                $code = '1';
                break;
        }

        if (!$code) {
            return null;
        }

        $subfield = $dom->createElement('subfield', $this->trimAndEscape($subfieldValue));
        $subfield->setAttribute('code', $code);
        $field->appendChild($subfield);

        return $field;
    }
}
