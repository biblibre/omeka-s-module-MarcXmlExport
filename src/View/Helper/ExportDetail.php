<?php
namespace MarcXmlExport\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class ExportDetail extends AbstractHelper
{
    public function __invoke($export)
    {
        $resourceTypo = [
            'item_sets' => 'item-set',
            'items' => 'item',
            'media' => 'media',
        ];

        $query = $resourceTypo[$export->resourceType()];

        if ($export->queryParams() !== '') {
            $query .= '?' . $export->queryParams();
        }

        $filePath = $export->filePath();

        return $this->getView()->partial(
            'marc-xml-export/admin/export/detail',
            [
                'id' => $export->id(),
                'name' => $export->name(),
                'mappingName' => $export->classMapping(),
                'resourceType' => $export->resourceType(),
                'resourceVisibility' => $export->resourceVisibility(),
                'query' => $query,
                'job' => $export->job(),
                'filePath' => $filePath,
            ]
        );
    }
}
