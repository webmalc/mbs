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
     * @return array
     * @todo return Doctrine Criteria
     */
    private function getTouristCriteria(Tourist $tourist, $queryBuilder = false)
    {
        $criteria = [];
        $documentRelation = $tourist->getDocumentRelation();
        if($documentRelation && $documentRelation->getType() && $documentRelation->getSeries() && $documentRelation->getNumber()) {
            $criteria['documentRelation.type'] = $documentRelation->getType();
            $criteria['documentRelation.series'] = $documentRelation->getSeries();
            $criteria['documentRelation.number'] = $documentRelation->getNumber();
        } else {
            $criteria['firstName'] = $tourist->getFirstName();
            $criteria['lastName'] = $tourist->getLastName();
            $criteria['birthday'] = $tourist->getBirthday();
        }

        if($queryBuilder) {
            $queryBuilder = $this->createQueryBuilder();
            foreach($criteria as $field => $equals) {
                $queryBuilder->field($field)->equals($equals);
            }
            return $queryBuilder;
        }

        return $criteria;
    }

    /**
     * @param Tourist $tourist
     * @return Unwelcome[]
     */
    public function findByTourist(Tourist $tourist)
    {
        return $this->findBy($this->getTouristCriteria($tourist));
    }

    /**
     * @param Tourist $tourist
     * @param Hotel $hotel
     * @return Unwelcome
     */
    public function findOneByTouristAndHotel(Tourist $tourist, Hotel $hotel)
    {
        return $this->findOneBy($this->getTouristCriteria($tourist) + ['hotel.id' => $hotel->getId()]);
    }

    /**
     * @param Tourist $tourist
     * @return bool
     */
    public function isUnwelcome(Tourist $tourist)
    {
        $queryBuilder = $this->getTouristCriteria($tourist, true);
        return $queryBuilder->getQuery()->count() > 0;
    }
}