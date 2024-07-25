<?php

$metadatasMapping = [
    'dcterms:type' => ['tag' => '099', 'repeatable' => false, 'subfield' => 't', 'repeatable_subfield' => false],
    'dcterms:language' => ['tag' => '101', 'repeatable' => false, 'subfield' => 'a', 'repeatable_subfield' => true],
    'dcterms:title' => ['tag' => '200', 'repeatable' => false, 'subfield' => 'a', 'repeatable_subfield' => true],
    'dcterms:alternative' => ['tag' => '200', 'repeatable' => false, 'subfield' => 'd', 'repeatable_subfield' => true],
    'dcterms:format' => ['tag' => '215', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => true],
    'dcterms:description' => ['tag' => '300', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
    'dcterms:provenance' => ['tag' => '317', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
    'dcterms:abstract' => ['tag' => '330', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
    'dcterms:rights' => ['tag' => '371', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
    'dcterms:rightsHolder' => ['tag' => '371', 'repeatable' => true, 'subfield' => 'b', 'repeatable_subfield' => false],
    'foaf:primaryTopic' => ['tag' => '600', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
    'foaf:theme' => ['tag' => '604', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
    'dcterms:subject' => ['tag' => '610', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => true],
    'foaf:topic' => ['tag' => '615', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
    'dcterms:spatial' => ['tag' => '660', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
    'dcterms:temporal' => ['tag' => '661', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
    'dcterms:creator' => ['tag' => '700', 'repeatable' => false, 'subfield' => 'a', 'repeatable_subfield' => false],
    'dcterms:contributor' => ['tag' => '702', 'repeatable' => true, 'subfield' => 'g', 'repeatable_subfield' => false],
    'dcterms:relation' => ['tag' => '856', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => true],
    'dcterms:instructionalMethod' => ['tag' => '901', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => true],
    'dcterms:source' => ['tag' => '995', 'repeatable' => true, 'subfield' => 'a', 'repeatable_subfield' => false],
    'bibo:locator' => ['tag' => '995', 'repeatable' => false, 'subfield' => 'k', 'repeatable_subfield' => false],
];

return $metadatasMapping;
