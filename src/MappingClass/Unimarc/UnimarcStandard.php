<?php

namespace MarcXmlExport\MappingClass\Unimarc;

use DOMDocument;
use MarcXmlExport\MappingClass\AbstractMappingClass;

class UnimarcStandard extends AbstractMappingClass
{
    protected $logger;
    protected $itemSetsTreeService;
    protected $dom;

    public function __construct($logger, $itemSetsTreeService)
    {
        $this->logger = $logger;
        $this->itemSetsTreeService = $itemSetsTreeService;
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
            if ($resource->isPublic()) {
                $record = $dom->createElement('record');
                $collection->appendChild($record);

                $resourceType = $resource->getResourceJsonLdType();
                                
                $fieldsMapping = $this->getFieldMapping($resource);
                $metadatasMapping = $this->getMetadatasMapping($resourceType);
                $repeatableFieldsMapping = $this->getRepeatableFieldsMapping($resourceType);

                foreach ($fieldsMapping as $tag => $value) {
                    $this->addElement($tag, $value, $record);
                }
                foreach ($repeatableFieldsMapping as $tag => $value) {
                    $this->addRepeatableElement($tag, $value, $record);
                }
                foreach ($metadatasMapping as $property => $mappingDatas) {
                    $this->addMetadataElement($resource, $property, $mappingDatas, $record);
                }
            }
        }
        header("Content-Type: text/xml");

        return $dom;
    }

    public function getLabel()
    {
        return 'UNIMARC Standard';
    }

    protected function addMetadataElement($resource, $property, $mappingDatas, $parentNode)
    {
        $dom = $this->getDom();

        $repeatableField = $mappingDatas['repeatable'];
        $repeatableSubfields = $mappingDatas['repeatable_subfield'];
        $code = $mappingDatas['subfield'];

        if (($repeatableField) || ($repeatableSubfields)) {
            $values = $resource->value($property, ['all' => true]);
        } else {
            $values = $resource->value($property);
        }

        if (! empty($values)) {
            $field = $this->getFieldByTag($mappingDatas['tag'], $parentNode);
            if (is_array($values)) {
                foreach ($values as $value) {
                    $subfield = $dom->createElement('subfield', $this->trimAndEscape($value));
                    $subfield->setAttribute('code', $code);
                    $field->appendChild($subfield);

                    if (explode(':', $value->type())[0] === 'valuesuggest') {
                        $extraMapping = $this->prepareValueSuggestValues($value);
                        foreach ($extraMapping as $code => $value) {
                            if (strlen($value) > 0) {
                                $extraSubfield = $dom->createElement('subfield', $this->trimAndEscape($value));
                                $extraSubfield->setAttribute('code', $code);
                                $field->appendChild($extraSubfield);
                            }
                        }
                    }

                    if ($repeatableField) {
                        $parentNode->appendChild($field);
                    }
                }

                if (! $repeatableField) {
                    $parentNode->appendChild($field);
                }
            } else {
                $value = $values;
                $subfield = $dom->createElement('subfield', $this->trimAndEscape($value));
                $subfield->setAttribute('code', $code);

                $field->appendChild($subfield);

                if (explode(':', $value->type())[0] === 'valuesuggest') {
                    $extraMapping = $this->prepareValueSuggestValues($value);
                    foreach ($extraMapping as $code => $value) {
                        if (strlen($value) > 0) {
                            $extraSubfield = $dom->createElement('subfield', $this->trimAndEscape($value));
                            $extraSubfield->setAttribute('code', $code);
                            $field->appendChild($extraSubfield);
                        }
                    }
                }

                if ($repeatableField) {
                    $parentNode->appendChild($field);
                }

                if (! $repeatableField) {
                    $parentNode->appendChild($field);
                }
            }
        }
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
    protected function addElement($tag, $value, $parentNode)
    {
        if (isset($value)) {
            $dom = $this->getDom();
            $element = $tag == '001' ? 'controlfield' : 'datafield';
            if (is_array($value)) {
                $field = $dom->createElement($element);
                foreach ($value as $subfieldKey => $subfieldValue) {
                    if (isset($subfieldValue)) {
                        $subfield = $dom->createElement('subfield', $this->trimAndEscape($subfieldValue));
                        $subfield->setAttribute('code', $subfieldKey);
                        $field->appendChild($subfield);
                    }
                }
            } else {
                $field = $dom->createElement($element, $this->trimAndEscape($value));
            }
            $field->setAttribute('tag', $tag);
            if ($field->hasChildNodes()) {
                $parentNode->appendChild($field);
            }
        }
    }

    protected function prepareValueSuggestValues($value)
    {
        $valueArray = $value->jsonSerialize();
        $extraMapping = [];

        $extraMapping['0'] = end(explode('/', $valueArray['@id']));
        $label = $valueArray['o:label'];

        if ($value->type() === 'valuesuggest:idref:person') {
            $extraMapping['a'] = explode(',', $label)[0];
            if (strpos($value, '(')) {
                $extraMapping['b'] = $this->getStringBetween($label, ',', '(');
                $extraMapping['d'] = $this->getStringBetween($label, '(', ')');
            } else {
                $extraMapping['b'] = explode(',', $label)[1];
            }
        } else {
            $extraMapping['a'] = $label;
            $extraMapping['u'] = $valueArray['@id'];
        }

        return $extraMapping;
    }

    protected function getDom()
    {
        return $this->dom;
    }

    protected function setDom($dom)
    {
        $this->dom = $dom;

        return $this;
    }

    protected function getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    protected function getFieldByTag($tag, $node)
    {
        $dom = $this->getDom();
        $datafields = $node->getElementsByTagName('datafield');
        $nodeToReturn = null;

        $newField = $dom->createElement('datafield');
        $newField->setAttribute('tag', $tag);

        foreach ($datafields as $datafield) {
            if ($datafield->getAttribute('tag') === $tag) {
                $nodeToReturn = $datafield;
            }
        }

        if (isset($nodeToReturn)) {
            return $nodeToReturn;
        } else {
            return $newField;
        }
    }

    protected function trimAndEscape($textValue)
    {
        if (strpos($textValue, '&') !== false) {
            $textValue = htmlspecialchars($textValue, ENT_XML1);
        }
        return trim($textValue);
    }

    protected function getFieldMapping($resource)
    {
        $resourceType = $resource->getResourceJsonLdType();
        $resourceTypeValues = [
            'o:ItemSet' => "collection",
            'o:Item' => "item",
            'o:Media' => "media",
        ];

        $fieldsMapping = [
            '001' => $resource->id(),
            '099' => ['c' => $resource->created()->format('Y-m-d'), 'd' => $resource->modified()->format('Y-m-d')],
            '299' => ['a' => "OMEKAS", 'b' => $resourceTypeValues[$resourceType]],
            '956' => ['u' => $resource->thumbnailDisplayUrl('medium')],
            '995' => ['a' => 'Bibliothèque numérique', 'b' => 'Bibliothèque numérique', 'f' => 'omekas-' . $resource->id(), 'r' => 'lz'],
        ];

        if ($resourceType === 'o:ItemSet') {
            $ancestors = $this->itemSetsTreeService->getAncestors($resource);
            if (isset($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $fieldsMapping['079'] = ['a' => $ancestor->id(), 'b' => "Collection parent", 'c' => $ancestor->title()];
                }
            }
        }
        if ($resourceType === 'o:Media') {
            $fieldsMapping['089'] = ['a' => $resource->item()->id(), 'b' => "Item", 'c' => $resource->item()->title()];
        }

        return $fieldsMapping;
    }

    protected function getMetadatasMapping($resourceType)
    {
        $metadatasMapping = [
            'dcterms:source' => ['tag' => '995', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'bibo:locator' => ['tag' => '995', 'repeatable' => false, 'subfield' => 'k', 'repeatable_subfield' => false],
            'dcterms:type' => ['tag' => '099', 'repeatable' => false, 'subfield' => 't', 'repeatable_subfield' => false],
            'dcterms:language' => ['tag' => '101', 'repeatable' => false, 'subfield' => 'a', 'repeatable_subfield' => true],
            'dcterms:title' => ['tag' => '200', 'repeatable' => false, 'subfield' => 'a', 'repeatable_subfield' => true],
            'dcterms:alternative' => ['tag' => '200', 'repeatable' => false, 'subfield' => 'd', 'repeatable_subfield' => true],
            'dcterms:format' => ['tag' => '215', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => true],
            'dcterms:description' => ['tag' => '300', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'dcterms:provenance' => ['tag' => '317', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'dcterms:abstract' => ['tag' => '330', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'foaf:primaryTopic' => ['tag' => '600', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'foaf:theme' => ['tag' => '604', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'dcterms:subject' => ['tag' => '610', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => true],
            'foaf:topic' => ['tag' => '615', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'dcterms:spatial' => ['tag' => '660', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'dcterms:temporal' => ['tag' => '661', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'dcterms:creator' => ['tag' => '700', 'repeatable' => false, 'subfield' => 'a', 'repeatable_subfield' => false],
            'dcterms:contributor' => ['tag' => '702', 'repeatable' => true, 'subfield' => 'g', 'repeatable_subfield' => false],
            'koha:biblionumber' => ['tag' => '035', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'dcterms:relation' => ['tag' => '856', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => true],
            'dcterms:instructionalMethod' => ['tag' => '901', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => true],
            'dcterms:rights' => ['tag' => '371', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
            'dcterms:rightsHolder' => ['tag' => '371', 'repeatable' => true, 'subfield' => 'b', 'repeatable_subfield' => false],
        ];

        if ($resourceType === 'o:ItemSet') {
            $metadatasMapping['dcterms:resume'] = ['tag' => '300', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false];
            $metadatasMapping['dcterms:date'] = ['tag' => '214', 'repeatable' => true, 'subfield' => 'd', 'repeatable_subfield' => true];
        }
        if ($resourceType === 'o:Item') {
            $metadatasMapping['dcterms:publisher'] = ['tag' => '214', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false];
            $metadatasMapping['dcterms:issued'] = ['tag' => '214', 'repeatable' => true, 'subfield' => 'd', 'repeatable_subfield' => true];
        }
        if ($resourceType === 'o:Media') {
            $metadatasMapping['dcterms:medium'] = ['tag' => '324', 'repeatable' => false, 'subfield' => 'a', 'repeatable_subfield' => false];
            $metadatasMapping['bibo:owner'] = ['tag' => '345', 'repeatable' => false, 'subfield' => 'a', 'repeatable_subfield' => true];
        }

        return $metadatasMapping;
    }

    protected function getRepeatableFieldsMapping($resource)
    {
        $resourceType = $resource->getResourceJsonLdType();

        if ($resourceType === 'o:Item') {
            if ($resource->itemSets()) {
                foreach ($resource->itemSets() as $itemSet) {
                    $repeatableFieldsMapping['069'][] = ['a' => $itemSet->id(), 'b' => "Collection", 'c' => $itemSet->title()];
                }
            }
        }
        if ($resourceType === 'o:ItemSet') {
            $ancestors = $this->itemSetsTreeService->getAncestors($resource);
            if (isset($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $repeatableFieldsMapping['079'][] = ['a' => $ancestor->id(), 'b' => "Collection parent", 'c' => $ancestor->title()];
                }
            }
        }

        return $repeatableFieldsMapping;
    }
}
