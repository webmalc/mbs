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
 * @ODM\Document(collection="PirateClient")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class PirateClient extends Base
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
     * @Assert\Ip()
     */
    protected $serverIp;


    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\Ip()
     */
    protected $userIp;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ODM\String()
     * @Assert\Url()
     */
    protected $url;

    /**
     * Set serverIp
     *
     * @param string $serverIp
     * @return self
     */
    public function setServerIp($serverIp)
    {
        $this->serverIp = $serverIp;
        return $this;
    }

    /**
     * Get serverIp
     *
     * @return string $serverIp
     */
    public function getServerIp()
    {
        return $this->serverIp;
    }

    /**
     * Set userIp
     *
     * @param string $userIp
     * @return self
     */
    public function setUserIp($userIp)
    {
        $this->userIp = $userIp;
        return $this;
    }

    /**
     * Get userIp
     *
     * @return string $userIp
     */
    public function getUserIp()
    {
        return $this->userIp;
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
}
