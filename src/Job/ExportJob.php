<?php

namespace MarcXmlExport\Job;

use Omeka\Job\AbstractJob;
use DateTime;

class ExportJob extends AbstractJob
{
    public function perform()
    {
        $services = $this->getServiceLocator();
        $api = $services->get('Omeka\ApiManager');
        $logger = $services->get('Omeka\Logger');
        $store = $services->get('Omeka\File\Store');

        $exporter = $services->get('MarcXmlExport\Exporter');

        $logger->info('Job started');

        $filename = tempnam(sys_get_temp_dir(), 'omekas_marc_xml_export');

        $exportName = $this->getArg('export_name');
        $resourceType = $this->getArg('resource_type');
        $classMapping = $this->getArg('class_mapping');
        $queryParams = $this->getArg('query_params');

        $xmlOutput = $exporter->exportQuery($resourceType, $queryParams, $classMapping);
        $xmlOutput->formatOutput = true;
        $xmlOutput->save($filename);

        $date = new DateTime('now');
        $now = $date->format('Y-m-d');
        $exportFile = sprintf("Marc_XML_Export/%s_%s.marcxml", $exportName, $now);
        $store->put($filename, $exportFile);

        $exportPath = $store->getUri($exportFile);

        $exportBackup = [
            'name' => $exportName,
            'resource_type' => $resourceType,
            'class_mapping' => $classMapping,
            'query_params' => $queryParams,
            'o:job' => ['o:id' => $this->job->getId()],
            'file_path' => $exportPath,
            'created' => $date,
        ];

        $api->create('marc_xml_export_exports', $exportBackup);

        $logger->info(sprintf("Exported to: %s", $exportPath));
        $logger->info('Job ended');
    }
}
