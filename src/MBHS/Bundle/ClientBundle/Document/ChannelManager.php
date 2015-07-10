<?php

namespace MBHS\Bundle\ClientBundle\Document;

use MBHS\Bundle\BaseBundle\Document\BaseDocument as Base;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableDocument;
use Gedmo\Blameable\Traits\BlameableDocument;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

/**
 * @ODM\Document(collection="ChannelManager")
 * @Gedmo\Loggable
 * @MongoDBUnique(fields={"title", "key"}, message="The channelmanager with the same title & key already exists")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ChannelManager extends Base
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
     * @Assert\Length(
     *      min=2,
     *      minMessage="Too short title",
     *      max=100,
     *      maxMessage="Too long title"
     * )
     */
    protected $title;

    /**
     * @var int
     * @Gedmo\Versioned
     * @ODM\Int()
     * @Assert\Type(type="numeric")
     * @Assert\NotNull()
     */
    protected $key;

    /**
     * @Gedmo\Versioned
     * @ODM\ReferenceOne(targetDocument="MBHS\Bundle\ClientBundle\Document\Client", inversedBy="channelManagers")
     * @Assert\NotNull()
     */
    protected $client;

    /**
     * Set title
     *
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set key
     *
     * @param int $key
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get key
     *
     * @return int $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set client
     *
     * @param \MBHS\Bundle\ClientBundle\Document\Client $client
     * @return self
     */
    public function setClient(\MBHS\Bundle\ClientBundle\Document\Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get client
     *
     * @return \MBHS\Bundle\ClientBundle\Document\Client $client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function getName()
    {
        return empty($this->getId()) ? 'New ChannelManager' :  $this->client . ': ' . $this->title;
    }
}
