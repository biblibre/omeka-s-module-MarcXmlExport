<?php
/**
 * Perform an export since specific date, resource and mapping class.
 */
require dirname(__DIR__, 4) . '/bootstrap.php';

$application = Omeka\Mvc\Application::init(require OMEKA_PATH . '/application/config/application.config.php');
$serviceLocator = $application->getServiceManager();
$em = $serviceLocator->get('Omeka\EntityManager');
$mappingFactories = array_keys($serviceLocator->get('Config')['marcxmlexport_mapping']['factories']);
$exporter = $serviceLocator->get('MarcXmlExport\Exporter');

$options = getopt(null, ['help', 'resource-type:', 'mapping-class:', 'since-date:', 'base-path:', 'server-url:']);
$entitiesMap = [
    'item_sets' => 'Omeka\Entity\ItemSet',
    'items' => 'Omeka\Entity\Item',
    'media' => 'Omeka\Entity\Media',
];

$now = date('Ymd');
$prefixExport = 'total';

function help()
{
    return <<<'HELP'

    export_since_date --base-path BASE_PATH --server-url SERVER_URL --resource-type RESOURCE_TYPE --mapping-class MAPPING_CLASS --since-date DATE
    export_since_date --help

    Options:
    --base-path BASE_PATH
        Define omeka base path.

    --server-url SERVER_URL
        Required. Define server url like http://myOmeka.com.

    --resource-type RESOURCE_TYPE
        Required. Choose which resource type will be exported ('item_sets', 'items' or 'media')

    --mapping-class MAPPING_CLASS
        Required. Choose which mapping class will be used (mapping class installed from module manager)

    --since-date DATE
        Optionnal. Set the date from which you wish to export (e.g. '2023-06-21')

    --help
        Display this help

    HELP;
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

if (isset($options['help'])) {
    echo help();
    exit;
}

if (!isset($options['server-url'])) {
    fprintf(STDERR, "No server URL given; use --server-url <serverUrl>\n");
    echo help();
    exit(1);
}

if (!isset($options['resource-type'])) {
    fprintf(STDERR, "No resource type given ; use --resource-type <resourceType> ('item_sets', 'items' or 'media')\n");
    echo help();
    exit(1);
}

if (!array_key_exists($options['resource-type'], $entitiesMap)) {
    fprintf(STDERR, "Resource not supported ; set one of this values: 'item_sets', 'items' or 'media'\n");
    exit(1);
}

if (!isset($options['mapping-class'])) {
    fprintf(STDERR, "No mapping class  given; use --mapping-class <mappingClass> (mapping class installed from module manager)\n");
    exit(1);
}

if (isset($options['since-date']) && !(validateDate($options['since-date']))) {
    fprintf(STDERR, "Date does not match 'Y-m-d' format or is not valid\n");
    exit(1);
}

$viewHelperManager = $serviceLocator->get('ViewHelperManager');

if (isset($options['base-path'])) {
    $base_path = "/" . $options['base-path'];
    $viewHelperManager->get('BasePath')->setBasePath($base_path);
    $serviceLocator->get('Router')->setBaseUrl($base_path);
}

$serverUrlParts = parse_url($options['server-url']);
$scheme = $serverUrlParts['scheme'];
$host = $serverUrlParts['host'];
if (isset($serverUrlParts['port'])) {
    $port = $serverUrlParts['port'];
} elseif ($serverUrlParts['scheme'] === 'http') {
    $port = 80;
} elseif ($serverUrlParts['scheme'] === 'https') {
    $port = 443;
} else {
    $port = null;
}
$serverUrlHelper = $viewHelperManager->get('ServerUrl');
$serverUrlHelper->setPort($port);
$serverUrlHelper->setScheme($scheme);
$serverUrlHelper->setHost($host);

$resourceType = $options['resource-type'];

$dql = "
        SELECT e.id FROM $entitiesMap[$resourceType] e 
    ";

if (isset($options['since-date'])) {
    $dql .= "WHERE (e.created >= :date OR e.modified >= :date)";
}

$doctrineQuery = $em->createQuery($dql);

if (isset($options['since-date'])) {
    $prefixExport = 'incr';
    $doctrineQuery->setParameter('date', $options['since-date']);
}

$iterableResults = $doctrineQuery->getArrayResult();
$ids = [];
foreach ($iterableResults as $result) {
    $ids[] = $result['id'];
}
if (!empty($ids)) {
    $query = http_build_query(['id' => $ids], '', '&');

    try {
        $tempFilename = "$now-omeka_notices_$prefixExport.marcxml";
        $xmlTempFile = tempnam(sys_get_temp_dir(), $tempFilename);

        if (!isset($options['since-date'])) {
            $query = null;
        }
        $xmlOutput = $exporter->exportQuery($resourceType, $query, $options['mapping-class']);

        $xmlOutput->formatOutput = true;
        $xmlOutput->save($xmlTempFile);
        echo $xmlTempFile;
    } catch (\Exception $e) {
        fprintf(STDERR, "Error: %s\n%s is currently configured\n", $e->getMessage(), implode(', ', $mappingFactories));
    }
} else {
    throw new Exception("No resources to export", 1);
}
