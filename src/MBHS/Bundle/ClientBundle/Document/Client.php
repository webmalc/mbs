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
 * @ODM\Document(collection="Client")
 * @Gedmo\Loggable
 * @MongoDBUnique(fields="title", message="The client with the same title already exists")
 * @MongoDBUnique(fields="email", message="The client with the same e-mail already exists")
 * @MongoDBUnique(fields="url", message="The client with the same url already exists")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Client extends Base
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
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     * @Assert\Regex(pattern="/[0-9\-\(\)\+]/", message="Invalid number")
     * @Assert\Length(
     *      min=7,
     *      minMessage="Too short phone",
     *      max=100,
     *      maxMessage="Too long phone"
     * )
     */
    protected $phone;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     * @Assert\Url()
     */
    protected $url;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     * @Assert\Ip()
     */
    protected $ip;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\NotNull()
     * @Assert\Length(
     *      min=40,
     *      minMessage="The key must be 40 characters long",
     *      max=40,
     *      maxMessage="The key must be 40 characters long"
     * )
     */
    protected $key;

    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ODM\Boolean()
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     */
    protected $isEnabled = true;

    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ODM\Date()
     * @Assert\Date()
     */
    protected $lastLogin;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\Length(min=3)
     */
    protected $person;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     */
    protected $note;

    /**
     * @Gedmo\Versioned
     * @ODM\ReferenceOne(targetDocument="MBHS\Bundle\BaseBundle\Document\Version", inversedBy="clients")
     * @Assert\NotNull()
     */
    protected $version;

    /**
     * @var array
     * @ODM\ReferenceMany(targetDocument="MBHS\Bundle\ClientBundle\Document\ChannelManager", mappedBy="client")
     */
    protected $channelManagers;

    /**
     * @var array
     * @ODM\ReferenceMany(targetDocument="MBHS\Bundle\ClientBundle\Document\Package", mappedBy="client")
     */
    protected $packages;

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
     * Set email
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = preg_replace("/[^0-9]/", "", $phone);

        return $this;
    }

    /**
     * Get phone
     *
     * @param boolean $original
     * @return string $phone
     */
    public function getPhone($original = false)
    {
        $phone = preg_replace("/[^0-9]/", "", $this->phone);

        if ($original || strlen($phone) < 7) {
            return $this->phone;
        } else {
            return empty($phone) ? null : '+ ' . substr($phone, 0, strlen($phone) - 7) . ' ' .
            substr($phone, -7, 3) . '-' .
            substr($phone, -4, 2) . '-' .
            substr($phone, -2, 2);
        }
    }

    /**
     * Set url
     *
     * @param string $url
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return self
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Get ip
     *
     * @return string $ip
     */
    public function getIp()
    {
        return $this->ip;
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
     * Set isEnabled
     *
     * @param boolean $isEnabled
     * @return self
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = (boolean) $isEnabled;
        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean $isEnabled
     */
    public function getIsEnabled()
    {
        return (boolean)$this->isEnabled;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return self
     */
    public function setLastLogin(\DateTime $lastLogin)
    {
        $this->lastLogin = $lastLogin;
        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime $lastLogin
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function getName()
    {
        return empty($this->getTitle()) ? 'New client' : $this->getTitle();
    }

    public function __construct()
    {
        $this->channelManagers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add channelManager
     *
     * @param \MBHS\Bundle\ClientBundle\Document\ChannelManager $channelManager
     */
    public function addChannelManager(\MBHS\Bundle\ClientBundle\Document\ChannelManager $channelManager)
    {
        $this->channelManagers[] = $channelManager;
    }

    /**
     * Remove channelManager
     *
     * @param \MBHS\Bundle\ClientBundle\Document\ChannelManager $channelManager
     */
    public function removeChannelManager(\MBHS\Bundle\ClientBundle\Document\ChannelManager $channelManager)
    {
        $this->channelManagers->removeElement($channelManager);
    }

    /**
     * Get channelManagers
     *
     * @return \Doctrine\Common\Collections\Collection $channelManagers
     */
    public function getChannelManagers()
    {
        return $this->channelManagers;
    }


    /**
     * Add package
     *
     * @param \MBHS\Bundle\ClientBundle\Document\Package $package
     */
    public function addPackage(\MBHS\Bundle\ClientBundle\Document\Package $package)
    {
        $this->packages[] = $package;
    }

    /**
     * Remove package
     *
     * @param \MBHS\Bundle\ClientBundle\Document\Package $package
     */
    public function removePackage(\MBHS\Bundle\ClientBundle\Document\Package $package)
    {
        $this->packages->removeElement($package);
    }

    /**
     * Get packages
     *
     * @return \Doctrine\Common\Collections\Collection $packages
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @return string
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param string $person
     * @return self
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     * @return self
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Set version
     *
     * @param \MBHS\Bundle\BaseBundle\Document\Version $version
     * @return self
     */
    public function setVersion(\MBHS\Bundle\BaseBundle\Document\Version $version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Get version
     *
     * @return \MBHS\Bundle\BaseBundle\Document\Version $version
     */
    public function getVersion()
    {
        return $this->version;
    }
}
