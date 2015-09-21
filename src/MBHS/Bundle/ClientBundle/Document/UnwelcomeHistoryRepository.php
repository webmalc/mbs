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
     * @return array
     * @todo return Doctrine Criteria
     */
    private function getTouristCriteria(Tourist $tourist)
    {
        $criteria = [];
        $documentRelation = $tourist->getDocumentRelation();
        if($documentRelation && $documentRelation->getType() && $documentRelation->getSeries() && $documentRelation->getNumber()) {
            $criteria['tourist.documentRelation.type'] = $documentRelation->getType();
            $criteria['tourist.documentRelation.series'] = $documentRelation->getSeries();
            $criteria['tourist.documentRelation.number'] = $documentRelation->getNumber();
        } else {
            $criteria['tourist.firstName'] = $tourist->getFirstName();
            $criteria['tourist.lastName'] = $tourist->getLastName();
            $criteria['tourist.birthday'] = $tourist->getBirthday();
        }

        return $criteria;
    }

    /**
     * @param Tourist $tourist
     * @return UnwelcomeHistory|null
     */
    public function findOneByTourist(Tourist $tourist)
    {
        return $this->findOneBy($this->getTouristCriteria($tourist));
    }

    /**
     * @param Tourist $tourist
     * @return bool
     */
    public function isUnwelcome(Tourist $tourist)
    {
        $queryBuilder = $this->createQueryBuilder();
        foreach($this->getTouristCriteria($tourist) as $field => $equals) {
            $queryBuilder->field($field)->equals($equals);
        }

        $queryBuilder->field('items')->exists(true);
        $queryBuilder->field('items')->not($queryBuilder->expr()->size(0));
        return $queryBuilder->getQuery()->count() > 0;
    }
}