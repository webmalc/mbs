<?php

namespace MBHS\Bundle\ClientBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Hotel
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 *
 * @ODM\EmbeddedDocument
 * @Gedmo\Loggable
 */
class Hotel implements \JsonSerializable
{
    /**
     * @var string
     * @ODM\String()
     */
    protected $title;

    /**
     * @var string
     * @ODM\String()
     */
    protected $city;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'title' => $this->title,
            'city' => $this->city,
        ];
    }
}