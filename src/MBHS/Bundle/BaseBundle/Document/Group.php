<?php

namespace MBHS\Bundle\BaseBundle\Document;

use Sonata\UserBundle\Document\BaseGroup as BaseGroup;
//use FOS\UserBundle\Document\Group as BaseGroup;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="groups")
 */
class Group extends BaseGroup
{
    /**
     * @ODM\Id(strategy="auto")
     */
    protected $id;

    /**
     * Get id
     */
    public function getId()
    {
        return $this->id;
    }

}