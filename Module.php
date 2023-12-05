<?php

/*
 * Copyright BibLibre, 2016-2023
 *
 * This software is governed by the CeCILL license under French law and abiding
 * by the rules of distribution of free software.  You can use, modify and/ or
 * redistribute the software under the terms of the CeCILL license as circulated
 * by CEA, CNRS and INRIA at the following URL "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and rights to copy, modify
 * and redistribute granted by the license, users are provided only with a
 * limited warranty and the software's author, the holder of the economic
 * rights, and the successive licensors have only limited liability.
 *
 * In this respect, the user's attention is drawn to the risks associated with
 * loading, using, modifying and/or developing or reproducing the software by
 * the user in light of its specific status of free software, that may mean that
 * it is complicated to manipulate, and that also therefore means that it is
 * reserved for developers and experienced professionals having in-depth
 * computer knowledge. Users are therefore encouraged to load and test the
 * software's suitability as regards their requirements in conditions enabling
 * the security of their systems and/or data to be ensured and, more generally,
 * to use and operate it in the same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
 */

namespace MarcXmlExport;

use Omeka\Module\AbstractModule;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');
        $sql = <<<'SQL'
        CREATE TABLE marc_xml_export_exports (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, created DATETIME NOT NULL, name VARCHAR(255) NOT NULL, query_params VARCHAR(255) DEFAULT NULL, resource_type VARCHAR(255) NOT NULL, class_mapping VARCHAR(255) NOT NULL, file_path VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_BCD99CFD5E237E06 (name), UNIQUE INDEX UNIQ_BCD99CFDBE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
        ALTER TABLE marc_xml_export_exports ADD CONSTRAINT FK_BCD99CFDBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id);

        SQL;

        $sqls = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($sqls as $sql) {
            $connection->exec($sql);
        }
    }

    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');
        $sql = <<<'SQL'
        ALTER TABLE marc_xml_export_exports DROP FOREIGN KEY FK_BCD99CFDBE04EA9;
        DROP TABLE IF EXISTS marc_xml_export_exports;
        SQL;

        $sqls = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($sqls as $sql) {
            $connection->exec($sql);
        }
    }
}
