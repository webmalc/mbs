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
 * Class Unwelcome
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 *
 * @ODM\Document(repositoryClass="UnwelcomeRepository")
 * @Gedmo\Loggable
 */
class Unwelcome extends Tourist implements \JsonSerializable
{
    /**
     * @var Client
     * @Gedmo\Versioned
     * @ODM\ReferenceOne(targetDocument="MBHS\Bundle\ClientBundle\Document\Client")
     * @Assert\NotNull()
     */
    protected $client;

    /**
     * @var Hotel
     * @ODM\ReferenceOne(targetDocument="MBHS\Bundle\ClientBundle\Document\Hotel", mappedBy="unwelcome")
     * @Assert\NotNull()
     */
    protected $hotel;

    /**
     * @var int
     * @ODM\Integer()
     */
    protected $foul;
    /**
     * @var int
     * @ODM\Integer()
     */
    protected $aggression;
    /**
     * @var int
     * @ODM\Integer()
     */
    protected $inadequacy;
    /**
     * @var int
     * @ODM\Integer()
     */
    protected $drunk;
    /**
     * @var int
     * @ODM\Integer()
     */
    protected $drugs;
    /**
     * @var int
     * @ODM\Integer()
     */
    protected $destruction;
    /**
     * @var int
     * @ODM\Integer()
     */
    protected $materialDamage;

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
     * @return int
     */
    public function getFoul()
    {
        return $this->foul;
    }

    /**
     * @param int $foul
     * @return $this
     */
    public function setFoul($foul)
    {
        $this->foul = $foul;
        return $this;
    }

    /**
     * @return int
     */
    public function getAggression()
    {
        return $this->aggression;
    }

    /**
     * @param int $aggression
     * @return $this
     */
    public function setAggression($aggression)
    {
        $this->aggression = $aggression;
        return $this;
    }

    /**
     * @return int
     */
    public function getInadequacy()
    {
        return $this->inadequacy;
    }

    /**
     * @param int $inadequacy
     * @return $this
     */
    public function setInadequacy($inadequacy)
    {
        $this->inadequacy = $inadequacy;
        return $this;
    }

    /**
     * @return int
     */
    public function getDrunk()
    {
        return $this->drunk;
    }

    /**
     * @param int $drunk
     * @return $this
     */
    public function setDrunk($drunk)
    {
        $this->drunk = $drunk;
        return $this;
    }

    /**
     * @return int
     */
    public function getDrugs()
    {
        return $this->drugs;
    }

    /**
     * @param int $drugs
     * @return $this
     */
    public function setDrugs($drugs)
    {
        $this->drugs = $drugs;
        return $this;
    }

    /**
     * @return int
     */
    public function getDestruction()
    {
        return $this->destruction;
    }

    /**
     * @param int $destruction
     * @return $this
     */
    public function setDestruction($destruction)
    {
        $this->destruction = $destruction;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaterialDamage()
    {
        return $this->materialDamage;
    }

    /**
     * @param int $materialDamage
     * @return $this
     */
    public function setMaterialDamage($materialDamage)
    {
        $this->materialDamage = $materialDamage;
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
        return parent::jsonSerialize() + [
            'foul' => $this->getFoul(),
            'aggression' => $this->getAggression(),
            'inadequacy' => $this->getInadequacy(),
            'drunk' => $this->getDrunk(),
            'drugs' => $this->getDrugs(),
            'destruction' => $this->getDestruction(),
            'materialDamage' => $this->getMaterialDamage(),
            'comment' => $this->getComment(),
            'touristCitizenship' => $this->getTouristCitizenship(),
            'touristEmail' => $this->getTouristEmail(),
            'touristPhone' => $this->getTouristPhone(),
            'createdAt' => $this->getCreatedAt() ? $this->getCreatedAt()->format('d.m.Y') : null,
            'hotel' => $this->getHotel(),
            'arrivalTime' => $this->getArrivalTime() ? $this->getArrivalTime()->format('d.m.Y') : null,
            'departureTime' => $this->getDepartureTime() ? $this->getDepartureTime()->format('d.m.Y') : null,
        ];
    }
}