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
 * @ODM\Document(collection="ClientConfig")
 * @Gedmo\Loggable
 * @MongoDBUnique(fields="title", message="Клиент с таким названием уже существует")
 * @MongoDBUnique(fields="email", message="Клиент с таким e-mail уже существует")
 * @MongoDBUnique(fields="url", message="Клиент с таким url уже существует")
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
     *      minMessage="Слишком короткое имя",
     *      max=100,
     *      maxMessage="Слишком длинное имя"
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
     * @Assert\Length(
     *      min=2,
     *      minMessage="Слишком короткий телефон",
     *      max=100,
     *      maxMessage="Слишком длинный телефон"
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
     *      minMessage="Слишком короткий ключ",
     *      max=40,
     *      maxMessage="Слишком длинный ключ"
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
     * @var int
     * @Gedmo\Versioned
     * @ODM\Int()
     * @Assert\Type(type="numeric")
     * @Assert\Range(
     *      min=0,
     *      minMessage="Количество смс не может быть меньше нуля"
     * )
     */
    protected $smsCount;

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
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
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
        $this->isEnabled = $isEnabled;
        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean $isEnabled
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set smsCount
     *
     * @param int $smsCount
     * @return self
     */
    public function setSmsCount($smsCount)
    {
        $this->smsCount = $smsCount;
        return $this;
    }

    /**
     * Get smsCount
     *
     * @return int $smsCount
     */
    public function getSmsCount()
    {
        return $this->smsCount;
    }
}
