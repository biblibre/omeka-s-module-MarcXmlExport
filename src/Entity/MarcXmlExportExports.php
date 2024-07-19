<?php
namespace MarcXmlExport\Entity;

use Omeka\Entity\AbstractEntity;
use DateTime;

/**
 * @Entity
 */
class MarcXmlExportExports extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @Column(type="datetime")
     */
    protected $created;

    /**
     * @Column(type="string", unique=true)
     * @JoinColumn(nullable=false)
     */
    protected $name;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $queryParams;

    /**
     * @Column(type="string")
     * @JoinColumn(nullable=false)
     */
    protected $resourceType;

    /**
     * @Column(type="string")
     * @JoinColumn(nullable=false)
     */
    protected $resourceVisibility;

    /**
     * @Column(type="string")
     * @JoinColumn(nullable=false)
     */
    protected $classMapping;

    /**
     * @Column(type="string")
     * @JoinColumn(nullable=false)
     */
    protected $filePath;

    /**
     * @OneToOne(targetEntity="Omeka\Entity\Job")
     * @JoinColumn(nullable=false)
     */
    protected $job;

    public function getId()
    {
        return $this->id;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated(DateTime $created)
    {
        $this->created = $created;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function setQueryParams($queryParams)
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    public function getResourceType()
    {
        return $this->resourceType;
    }

    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;

        return $this;
    }

    public function getResourceVisibility()
    {
        return $this->resourceVisibility;
    }

    public function setResourceVisibility($resourceVisibility)
    {
        $this->resourceVisibility = $resourceVisibility;

        return $this;
    }

    public function getClassMapping()
    {
        return $this->classMapping;
    }

    public function setClassMapping($classMapping)
    {
        $this->classMapping = $classMapping;

        return $this;
    }

    public function getJob()
    {
        return $this->job;
    }

    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }
}
