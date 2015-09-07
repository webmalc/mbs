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
 * @ODM\Document(collection="Unwelcome", repositoryClass="MBHS\Bundle\ClientBundle\Document\UnwelcomeRepository")
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
     * @var Tourist
     * @ODM\EmbedOne(targetDocument="MBHS\Bundle\ClientBundle\Document\Tourist")
     */
    protected $tourist;

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
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Tourist|null
     */
    public function getTourist()
    {
        return $this->tourist;
    }

    /**
     * @param Tourist|null $tourist
     */
    public function setTourist(Tourist $tourist = null)
    {
        $this->tourist = $tourist;
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
        ];
    }
}