<?php

namespace MarcXmlExport\Job;

use DateTime;
use Omeka\Job\AbstractJob;
use DoctrineProxies\__CG__\MarcXmlExport\Entity\MarcXmlExportExports;

class RemoveFilesJob extends AbstractJob
{
    public function perform()
    {
        $help = $this->getArg('help');
        if ($help) {
            echo $this->usageHelp();
            return;
        }

        $services = $this->getServiceLocator();
        $em = $this->getServiceLocator()->get('Omeka\EntityManager');
        $logger = $services->get('Omeka\Logger');
        $store = $services->get('Omeka\File\Store');

        $exportRepository = $em->getRepository(MarcXmlExportExports::class);
        $dateArg = $this->getArg('date');

        if (empty($dateArg)) {
            throw new \Exception('You must add an argument date');
            return;
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $dateArg);
        }

        $exportsToDelete = $this->findExportsBeforeDate($date, $exportRepository);

        if (count($exportsToDelete) > 0) {
            $exportsId = [];

            foreach ($exportsToDelete as $export) {
                $exportsId[] = $export['id'];
                $localFilePath = "Marc_XML_Export/" . pathinfo($export['filePath'])['filename'] . ".xml";
                if (file_exists($localFilePath)) {
                    $store->delete($localFilePath);
                    $logger->info(sprintf('%s has been deleted', $export['filePath']));
                }
            }

            $this->deleteSQLEntries($exportsId, $exportRepository);
            $logger->info(sprintf('Ids deleted from marc_xml_export_exports table: ( %s )', implode(';', $exportsId)));
        } else {
            $logger->info(sprintf('No exports found before the %s ', $date));
        }
    }

    private function findExportsBeforeDate(\DateTime $date, $exportRepository)
    {
        return $exportRepository
            ->createQueryBuilder('m')
            ->select('m.id', 'm.filePath')
            ->where("m.created < ?1")
            ->setParameter(1, $date)
            ->getQuery()
            ->getResult();
    }

    private function deleteSQLEntries($exportsId, $exportRepository)
    {
        return $exportRepository
            ->createQueryBuilder('m')
            ->delete()
            ->where("m.id IN (?1)")
            ->setParameter(1, $exportsId)
            ->getQuery()
            ->execute();
    }

    private function usageHelp()
    {
        return <<<EOF
            Usage:
                php remove-files-job.php [--date=YYYY-MM-DD] [--days=NN] [--help]
            Options:
                --date=YYYY-MM-DD  Supprime les fichiers datant d'avant la date spécifiée.
                --days=NN         Supprime les fichiers datant de plus de NN jours.
                --help            Affiche cette aide.
        EOF;
    }
}
