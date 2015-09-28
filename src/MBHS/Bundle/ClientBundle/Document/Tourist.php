<?php

namespace MBHS\Bundle\ClientBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use MBHS\Bundle\BaseBundle\Document\BaseDocument;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Hotel
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 *
 * @ODM\MappedSuperclass
 * @Gedmo\Loggable()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Tourist extends BaseDocument implements \JsonSerializable
{
    use TimestampableDocument;
    use SoftDeleteableDocument;
    use BlameableDocument;

    /**
     * @var string
     * @ODM\String()
     */
    protected $firstName;
    /**
     * @var string
     * @ODM\String()
     */
    protected $lastName;
    /**
     * @var string
     * @ODM\String()
     */
    protected $patronymic;
    /**
     * @var \DateTime|null
     * @ODM\Date()
     */
    protected $birthday;
    /**
     * @var string
     * @ODM\String()
     */
    protected $phone;
    /**
     * @var string
     * @ODM\String()
     */
    protected $email;
    /**
     * @var string
     * @ODM\String()
     */
    protected $communicationLanguage;

    /**
     * @var string
     * @ODM\String()
     */
    protected $citizenship;

    /**
     * @var DocumentRelation
     * @ODM\EmbedOne(targetDocument="DocumentRelation")
     */
    protected $documentRelation;

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * @param mixed $patronymic
     * @return self
     */
    public function setPatronymic($patronymic)
    {
        $this->patronymic = $patronymic;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime|null $birthday
     * @return self
     */
    public function setBirthday(\DateTime $birthday = null)
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommunicationLanguage()
    {
        return $this->communicationLanguage;
    }

    /**
     * @param mixed $communicationLanguage
     * @return self
     */
    public function setCommunicationLanguage($communicationLanguage)
    {
        $this->communicationLanguage = $communicationLanguage;
        return $this;
    }

    /**
     * @return string
     */
    public function getCitizenship()
    {
        return $this->citizenship;
    }

    /**
     * @param string $citizenship
     * @return $this
     */
    public function setCitizenship($citizenship)
    {
        $this->citizenship = $citizenship;
        return $this;
    }

    /**
     * @return DocumentRelation|null
     */
    public function getDocumentRelation()
    {
        return $this->documentRelation;
    }

    /**
     * @param DocumentRelation|null $documentRelation
     * @return $this
     */
    public function setDocumentRelation(DocumentRelation $documentRelation = null)
    {
        $this->documentRelation = $documentRelation;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'patronymic' => $this->patronymic,
            'birthday' => $this->getBirthday() ? $this->getBirthday()->format('d.m.Y') : null,
            'phone' => $this->phone,
            'email' => $this->email,
            'citizenship' => $this->citizenship,
            'documentRelation' => $this->getDocumentRelation()
        ];
    }
}