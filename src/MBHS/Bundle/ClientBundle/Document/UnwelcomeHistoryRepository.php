<?php

namespace MBHS\Bundle\ClientBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class UnwelcomeHistoryRepository
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class UnwelcomeHistoryRepository extends DocumentRepository
{
    /**
     * @param Tourist $tourist
     * @return UnwelcomeHistory|null
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