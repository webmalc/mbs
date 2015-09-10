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

    /**
     * @param Tourist $tourist
     * @return bool
     */
    public function isUnwelcome(Tourist $tourist)
    {
        $queryBuilder = $this->createQueryBuilder();
        $queryBuilder->field('tourist.firstName')->equals($tourist->getFirstName());
        $queryBuilder->field('tourist.lastName')->equals($tourist->getLastName());
        $queryBuilder->field('tourist.birthday')->equals($tourist->getBirthday());
        $queryBuilder->field('items')->exists(true);
        $queryBuilder->field('items')->not($queryBuilder->expr()->size(0));
        return $queryBuilder->getQuery()->count() > 0;
    }
}