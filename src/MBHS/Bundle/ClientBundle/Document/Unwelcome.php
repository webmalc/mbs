<?php

namespace MBHS\Bundle\ClientBundle\Document;

use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use MBHS\Bundle\BaseBundle\Document\BaseDocument as Base;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ODM\EmbeddedDocument
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Unwelcome extends Base implements \JsonSerializable
{
    /**
     * Hook timestampable behavior
     * up\DateTimes createdAt, up\DateTimedAt fields
     */
    use TimestampableDocument;

    /**
     * Hook softdeleteable behavior
     * deletedAt field
     */
    use SoftDeleteableDocument;

    /**
     * Hook blameable behavior
     * createdBy&up\DateTimedBy fields
     */
    use BlameableDocument;

    /**
     * @var Client
     * @Gedmo\Versioned
     * @ODM\ReferenceOne(targetDocument="MBHS\Bundle\ClientBundle\Document\Client", inversedBy="packages")
     * @Assert\NotNull()
     */
    protected $client;

    /**
     * @var bool
     * @ODM\Boolean()
     */
    protected $isAggressor;

    /**
     * @var string
     * @ODM\String()
     */
    protected $comment;

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client $client
     * @return self
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsAggressor()
    {
        return $this->isAggressor;
    }

    /**
     * @param boolean $isAggressor
     */
    public function setAggressor($isAggressor)
    {
        $this->isAggressor = $isAggressor;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }


    public function jsonSerialize()
    {
        return [
            'comment' => $this->getComment(),
            'isAggressor' => $this->getIsAggressor(),
            'createdAt' => $this->getCreatedAt() ? $this->getCreatedAt()->format('d.m.Y') : null,
        ];
    }
}