<?php

namespace MBHS\Bundle\ClientBundle\Document;

use MBHS\Bundle\BaseBundle\Document\BaseDocument as Base;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableDocument;
use Gedmo\Blameable\Traits\BlameableDocument;

/**
 * @ODM\Document(collection="Package")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Package extends Base
{
    const TYPES = ["online", "channel_manager", "offline"];

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
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     * @Assert\Choice(callback = "getTypes")
     */
    protected $type;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     */
    protected $key;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     */
    protected $number;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\Date()
     * @Assert\NotNull()
     * @Assert\DateTime()
     */
    protected $begin;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\Date()
     * @Assert\NotNull()
     * @Assert\DateTime()
     */
    protected $end;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     */
    protected $hotel;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     */
    protected $roomType;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     */
    protected $payer;

    /**
     * @var float
     * @Gedmo\Versioned
     * @ODM\Float()
     * @Assert\Type(type="numeric")
     * @Assert\NotNull()
     */
    protected $price;

    /**
     * @Gedmo\Versioned
     * @ODM\ReferenceOne(targetDocument="MBHS\Bundle\ClientBundle\Document\Client", inversedBy="packages")
     * @Assert\NotNull()
    */
    protected $client;

    /**
     * @return array
     */
    public function getTypes()
    {
        return self::TYPES;
    }
    
    public function getName()
    {
        return empty($this->getId()) ? 'New Package' :  $this->client . ': ' . $this->number;
    }
    
    /**
     * Set type
     *
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set key
     *
     * @param string $key
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
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return string $number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set begin
     *
     * @param \DateTime $begin
     * @return self
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;
        return $this;
    }

    /**
     * Get begin
     *
     * @return \DateTime $begin
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return self
     */
    public function setEnd($end)
    {
        $this->end = $end;
        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime $end
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set roomType
     *
     * @param string $roomType
     * @return self
     */
    public function setRoomType($roomType)
    {
        $this->roomType = $roomType;
        return $this;
    }

    /**
     * Get roomType
     *
     * @return string $roomType
     */
    public function getRoomType()
    {
        return $this->roomType;
    }

    /**
     * Set payer
     *
     * @param string $payer
     * @return self
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;
        return $this;
    }

    /**
     * Get payer
     *
     * @return string $payer
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return float $price
     */
    public function getPrice()
    {
        return $this->price;
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

    /**
     * @return string
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * @param string $hotel
     * @return self
     */
    public function setHotel($hotel)
    {
        $this->hotel = $hotel;

        return $this;
    }


}
