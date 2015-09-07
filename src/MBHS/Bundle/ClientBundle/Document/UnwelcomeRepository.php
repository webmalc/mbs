<?php

namespace MBHS\Bundle\ClientBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class UnwelcomeRepository
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class UnwelcomeRepository extends DocumentRepository
{
    /**
     * @param Tourist $tourist
     * @return Unwelcome|null
     */
    public function findOneByTourist(Tourist $tourist)
    {
        return $this->findOneBy([
            'tourist.firstName' => $tourist->getFirstName(),
            'tourist.lastName' => $tourist->getLastName(),
            'tourist.birthday' => $tourist->getBirthday(),
        ]);
    }
}