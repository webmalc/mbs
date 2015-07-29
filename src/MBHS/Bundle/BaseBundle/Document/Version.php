<?php

namespace MBHS\Bundle\BaseBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use MBHS\Bundle\BaseBundle\Document\BaseDocument as Base;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

/**
 * @ODM\Document(collection="Version")
 * @MongoDBUnique(fields="title", message="The version with the same number already exists")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Version extends Base
{
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableDocument;

    /**
     * Hook softdeleteable behavior
     * deletedAt field
     */
    use SoftDeleteableDocument;

    /**
     * Hook blameable behavior
     * createdBy&updatedBy fields
     */
    use BlameableDocument;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     */
    protected $title;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     */
    protected $description;

    /**
     * @var array
     * @ODM\ReferenceMany(targetDocument="MBHS\Bundle\ClientBundle\Document\Client", mappedBy="version")
     */
    protected $clients;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Version
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Version
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function __construct()
    {
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add client
     *
     * @param \MBHS\Bundle\ClientBundle\Document\Client $client
     */
    public function addClient(\MBHS\Bundle\ClientBundle\Document\Client $client)
    {
        $this->clients[] = $client;
    }

    /**
     * Remove client
     *
     * @param \MBHS\Bundle\ClientBundle\Document\Client $client
     */
    public function removeClient(\MBHS\Bundle\ClientBundle\Document\Client $client)
    {
        $this->clients->removeElement($client);
    }

    /**
     * Get clients
     *
     * @return \Doctrine\Common\Collections\Collection $clients
     */
    public function getClients()
    {
        return $this->clients;
    }

    public function __toString()
    {
        return $this->title;
    }
}
