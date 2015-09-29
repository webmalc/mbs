<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController;
use MBHS\Bundle\ClientBundle\Document\DocumentRelation;
use MBHS\Bundle\ClientBundle\Document\Hotel;
use MBHS\Bundle\ClientBundle\Document\Tourist;
use MBHS\Bundle\ClientBundle\Document\Unwelcome;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class BlackListController
 * @Route("/unwelcome")
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 * @todo move "getRequest.." methods to transformer service
 */
class UnwelcomeController extends BaseController
{
    private function getRequestData()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        return $this->get('serializer')->decode($request->getContent(), 'json');
    }

    /**
     * @return Tourist|null
     */
    private function getRequestTourist()
    {
        $data = $this->getRequestData();
        if($data && isset($data['tourist'])) {
            $data['tourist']['birthday'] = $this->get('mbhs.helper')->getDateFromString($data['tourist']['birthday']);
            /** @var Tourist $tourist */
            $documentRelationData = $data['tourist']['documentRelation'];
            unset($data['tourist']['documentRelation']);
            $tourist = $this->get('serializer')->denormalize($data['tourist'], Tourist::class);

            $documentRelationData['issued'] = $this->get('mbhs.helper')->getDateFromString($documentRelationData['issued']);
            $documentRelationData['expiry'] = $this->get('mbhs.helper')->getDateFromString($documentRelationData['expiry']);
            $documentRelationData['number'] = $documentRelationData['number'] ? (int) $documentRelationData['number'] : null;
            $documentRelation = $this->get('serializer')->denormalize($documentRelationData, DocumentRelation::class);
            $tourist->setDocumentRelation($documentRelation);
            return $tourist;
        }
        return null;
    }

    /**
     * @return Unwelcome|null
     */
    private function getRequestUnwelcome()
    {
        $data = $this->getRequestData();
        $unwelcome = null;
        if($data && isset($data['unwelcome'])) {
            $data = $data['unwelcome'];
            $unwelcome = new Unwelcome();
            $unwelcome
                ->setFoul($data['foul'])
                ->setAggression($data['aggression'])
                ->setInadequacy($data['inadequacy'])
                ->setDrunk($data['drunk'])
                ->setDrugs($data['drugs'])
                ->setDestruction($data['destruction'])
                ->setMaterialDamage($data['materialDamage'])
                ->setComment($data['comment'])
                ->setHotel($this->getRequestHotel())
                ->setClient($this->getClient())
            ;

            $tourist = $this->getRequestTourist();
            if(!$tourist) {
                return null;
            }

            $unwelcome->setFirstName($tourist->getFirstName());
            $unwelcome->setLastName($tourist->getLastName());
            $unwelcome->setPatronymic($tourist->getPatronymic());
            $unwelcome->setBirthday($tourist->getBirthday());
            $unwelcome->setCitizenship($tourist->getCitizenship());
            $unwelcome->setCommunicationLanguage($tourist->getCommunicationLanguage());
            $unwelcome->setDocumentRelation($tourist->getDocumentRelation());
            $unwelcome->setEmail($tourist->getEmail());
            $unwelcome->setPhone($tourist->getPhone());


            if(isset($data['arrivalTime']) && isset($data['departureTime'])) {
                $unwelcome
                    ->setArrivalTime($this->get('mbhs.helper')->getDateFromString($data['arrivalTime']))
                    ->setDepartureTime($this->get('mbhs.helper')->getDateFromString($data['departureTime']));
            }
        }

        return $unwelcome;
    }

    /**
     * @return Hotel|null
     */
    private function getRequestHotel()
    {
        $data = $this->getRequestData();
        $hotel = null;
        if($data && isset($data['hotel']) && $this->getClient()) {
            $hotelData = $data['hotel'];
            $hotel = $this->dm->getRepository('MBHSClientBundle:Hotel')->findOneBy(['internalID' => $hotelData['id'], 'client.id' => $this->getClient()->getId()]);
            if(!$hotel) {
                $hotel = new Hotel();
                $hotel
                    ->setInternalID($hotelData['id'])
                    ->setTitle($hotelData['title'])
                    ->setCity($hotelData['city'])
                    ->setClient($this->getClient());
                ;
                $this->dm->persist($hotel);
                $this->dm->flush();
            }
        }

        return $hotel;
    }

    /**
     * @return \MBHS\Bundle\ClientBundle\Document\UnwelcomeRepository
     */
    private function getUnwelcomeRepository()
    {
        return $this->dm->getRepository('MBHSClientBundle:Unwelcome');
    }

    /**
     * @return \MBHS\Bundle\ClientBundle\Document\Client|null
     */
    private function getClient()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $mbhsRequest = $this->container->get('mbhs.request');
        $client = $mbhsRequest->getClient($request);
        if($client) {
            return $client;
        } else {
            $mbhsRequest->addPirateClient($request);
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Route("/add")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction()
    {
        $requestUnwelcome = $this->getRequestUnwelcome();
        $requestHotel = $this->getRequestHotel();

        if(!$requestUnwelcome || !$requestHotel) {
            return new JsonResponse(['status' => false]);
        }

        if($this->getUnwelcomeRepository()->findOneByTouristAndHotel($requestUnwelcome, $requestHotel)) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Unwelcome message. Unwelcome for this tourist already exists.'
            ]);
        } else {
            //$requestUnwelcome->setHotel($requestHotel);
            $requestHotel->addUnwelcome($requestUnwelcome);
            $requestUnwelcome->setHotel($requestHotel);
            $this->dm->persist($requestHotel);
            $this->dm->persist($requestUnwelcome);
            $this->dm->flush();

            return new JsonResponse(['status' => true]);
        }
    }

    /**
     * @Route("/update")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction()
    {
        $requestUnwelcome = $this->getRequestUnwelcome();
        $unwelcome = $this->getUnwelcomeRepository()->findOneByTouristAndHotel($this->getRequestTourist(), $this->getRequestHotel());
        if($requestUnwelcome && $unwelcome) {
            //if($blackListInfo->getClient() == $client) {}
            $unwelcome
                ->setFoul($requestUnwelcome->getFoul())
                ->setAggression($requestUnwelcome->getAggression())
                ->setInadequacy($requestUnwelcome->getInadequacy())
                ->setDrunk($requestUnwelcome->getDrunk())
                ->setDrugs($requestUnwelcome->getDrugs())
                ->setDestruction($requestUnwelcome->getDestruction())
                ->setMaterialDamage($requestUnwelcome->getMaterialDamage())
                ->setComment($requestUnwelcome->getComment())
            ;

            $this->dm->persist($unwelcome);
            $this->dm->flush();
        }

        return new JsonResponse(['status' => true]);
    }

    /**
     * @Route("/find_by_tourist")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function findByTourist()
    {
        $unwelcomeList = [];
        if ($requestTourist = $this->getRequestTourist()) {
            foreach($this->getUnwelcomeRepository()->findByTourist($requestTourist) as $unwelcome) {
                /** @var Unwelcome $unwelcome */
                $unwelcomeList[] = $unwelcome->jsonSerialize() + ['isMy' => $unwelcome->getClient() == $this->getClient()];
            }
        }

        return new JsonResponse([
            'status' => true,
            'unwelcomeList' => $unwelcomeList
        ]);
    }

    /**
     * @Route("/delete_by_tourist")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteByTourist()
    {
        $client = $this->getClient();

        $tourist = $this->getRequestTourist();
        $hotel = $this->getRequestHotel();
        $successDeleted = false;
        if ($tourist && $hotel) {
            /** @var Unwelcome|null $unwelcome */
            $unwelcome = $this->getUnwelcomeRepository()->findOneByTouristAndHotel($tourist, $hotel);

            if($unwelcome && $unwelcome->getClient() == $client) {
                $this->dm->remove($unwelcome);
                $this->dm->flush();
                $successDeleted = true;
            }
        }

        return new JsonResponse([
            'status' => true,
            'successDeleted' => $successDeleted,
        ]);
    }

    /**
     * @Route("/has")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function hasUnwelcomeHistory()
    {
        $requestTourist = $this->getRequestTourist();
        if($requestTourist) {
            return new JsonResponse([
                'status' => true,
                'result' => $this->getUnwelcomeRepository()->isUnwelcome($requestTourist)
            ]);
        }
        return new JsonResponse([
            'status' => false,
            'error' => 'Expect request with tourist'
        ]);
    }
}