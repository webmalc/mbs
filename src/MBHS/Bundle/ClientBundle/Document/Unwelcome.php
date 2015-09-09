<?php

namespace MBHS\Bundle\ClientBundle\Document;

use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use MBHS\Bundle\BaseBundle\Document\BaseDocument as Base;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Unwelcome
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 *
 * @ODM\EmbeddedDocument
 * @Gedmo\Loggable
 */
class Unwelcome extends Base implements \JsonSerializable
{
    use TimestampableDocument;
    use BlameableDocument;

    /**
     * @var Client
     * @Gedmo\Versioned
     * @ODM\ReferenceOne(targetDocument="MBHS\Bundle\ClientBundle\Document\Client", inversedBy="packages")
     * @Assert\NotNull()
     */
    protected $client;

    /**
     * @var Hotel
     * @ODM\EmbedOne(targetDocument="MBHS\Bundle\ClientBundle\Document\Hotel")
     */
    protected $hotel;

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
     * @var \DateTime
     * @Gedmo\Versioned
     * @ODM\Date()
     * @Assert\DateTime()
     */
    protected $arrivalTime;

    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ODM\Date()
     * @Assert\DateTime()
     */
    protected $departureTime;

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Hotel|null
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * @param Hotel|null $hotel
     * @return $this
     */
    public function setHotel(Hotel $hotel = null)
    {
        $this->hotel = $hotel;
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
     * @return $this
     */
    public function setAggressor($isAggressor)
    {
        $this->isAggressor = $isAggressor;
        return $this;
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
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }


    /**
     * @param \DateTime $arrivalTime
     * @return $this
     */
    public function setArrivalTime(\DateTime $arrivalTime = null)
    {
        $this->arrivalTime = $arrivalTime;
        return $this;
    }

    /**
     * @return \DateTime $arrivalTime
     */
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * @param \DateTime $departureTime
     * @return $this
     */
    public function setDepartureTime(\DateTime $departureTime = null)
    {
        $this->departureTime = $departureTime;
        return $this;
    }

    /**
     * @return \DateTime $departureTime
     */
    public function getDepartureTime()
    {
        return $this->departureTime;
    }


    public function jsonSerialize()
    {
        return [
            'comment' => $this->getComment(),
            'isAggressor' => $this->getIsAggressor(),
            'createdAt' => $this->getCreatedAt() ? $this->getCreatedAt()->format('d.m.Y') : null,
            'hotel' => $this->getHotel(),
            'arrivalTime' => $this->getArrivalTime() ? $this->getArrivalTime()->format('d.m.Y') : null,
            'departureTime' => $this->getDepartureTime() ? $this->getDepartureTime()->format('d.m.Y') : null,
        ];
    }
}