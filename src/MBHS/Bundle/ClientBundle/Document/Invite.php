<?php

namespace MBHS\Bundle\ClientBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Invite
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 *
 * @ODM\Document()
 * @Gedmo\Loggable
 * @ODM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Invite
{
    const TYPE_SINGLE = 'single';
    const TYPE_TWICE = 'twice';

    use TimestampableDocument;
    use SoftDeleteableDocument;
    use BlameableDocument;

    /**
     * @ODM\Id()
     * @var
     */
    protected $id;

    /**
     * @var Hotel
     * @ODM\EmbedOne(targetDocument="MBHS\Bundle\ClientBundle\Document\Hotel")
     */
    protected $hotel;

    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ODM\Date
     * @Assert\Date()
     */
    protected $arrival;
    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ODM\Date
     * @Assert\Date()
     */
    protected $departure;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     */
    protected $type;
    /**
     * @ODM\EmbedMany(targetDocument="MBHS\Bundle\ClientBundle\Document\InvitedTourist")
     * @var InvitedTourist[]
     */
    protected $guests;

    /**
     * @ODM\EmbedMany(targetDocument="MBHS\Bundle\ClientBundle\Document\TripRoute")
     * @var TripRoute[]
     */
    protected $tripRoutes = [];

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Hotel
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * @param Hotel $hotel
     * @return $this
     */
    public function setHotel(Hotel $hotel = null)
    {
        $this->hotel = $hotel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getArrival()
    {
        return $this->arrival;
    }

    /**
     * @param \DateTime|null $arrival
     * @return $this
     */
    public function setArrival(\DateTime $arrival = null)
    {
        $this->arrival = $arrival;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * @param \DateTime|null $departure
     * @return $this
     */
    public function setDeparture($departure = null)
    {
        $this->departure = $departure;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return InvitedTourist[]
     */
    public function getGuests()
    {
        return $this->guests;
    }
    /**
     * @param InvitedTourist $guest
     */
    public function addGuest(InvitedTourist $guest)
    {
        $this->guests[] = $guest;
    }
    /**
     * @param InvitedTourist[] $guests
     * @return $this
     */
    public function setGuests($guests)
    {
        $this->guests = $guests;
        return $this;
    }

    /**
     * @return TripRoute[]
     */
    public function getTripRoutes()
    {
        return $this->tripRoutes;
    }

    /**
     * @param TripRoute[] $tripRoutes
     * @return $this
     */
    public function setTripRoutes($tripRoutes)
    {
        $this->tripRoutes = $tripRoutes;
        return $this;
    }

    /**
     * @param TripRoute $tripRoute
     * @return $this
     */
    public function addTripRoute(TripRoute $tripRoute)
    {
        $this->tripRoutes[] = $tripRoute;
        return $this;
    }
}