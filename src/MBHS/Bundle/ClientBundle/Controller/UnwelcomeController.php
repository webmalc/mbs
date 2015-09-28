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
        if($data && isset($data['hotel'])) {
            $hotel = new Hotel();
            $hotel
                ->setTitle($data['hotel']['title'])
                ->setCity($data['hotel']['city']);
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
            return $this->createAccessDeniedException();
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

        if(false) { //$this->getUnwelcomeRepository()->findOneByTourist($requestTourist)
            return new JsonResponse([
                'status' => false,
                'message' => 'Can not add new unwelcome. Unwelcome for this tourist already exists.'
            ]);
        } else {
            dump($requestHotel);
            //$requestUnwelcome->setHotel($requestHotel);
            $requestHotel->addUnwelcome($requestUnwelcome);
            $this->dm->persist($requestHotel);
            //$this->dm->persist($requestUnwelcome);
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
        $client = $this->getClient();

        $requestUnwelcome = $this->getRequestUnwelcome();
        $requestTourist = $this->getRequestTourist();

        $unwelcome = null;
        $unwelcomeHistory = $this->getUnwelcomeRepository()->findOneByTourist($requestTourist);
        if ($unwelcomeHistory) {
            foreach ($unwelcomeHistory->getItems() as $u) {
                if ($u->getClient() == $client) {
                    $unwelcome = $u;
                    break;
                }
            }

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
                    ->setTouristCitizenship($requestUnwelcome->getTouristCitizenship())
                    ->setTouristPhone($requestUnwelcome->getTouristPhone())
                    ->setTouristEmail($requestUnwelcome->getTouristEmail())
                    ->setComment($requestUnwelcome->getComment())
                ;

                $this->dm->persist($unwelcomeHistory);
                $this->dm->persist($unwelcome);
                $this->dm->flush();
            }
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
        $client = $this->getClient();
        $unwelcomeHistory = null;

        $requestTourist = $this->getRequestTourist();
        if ($requestTourist) {
            /** @var Unwelcome|null $unwelcomeHistory */
            $unwelcomeHistory = $this->getUnwelcomeRepository()->findOneByTourist($requestTourist);
        }

        if ($unwelcomeHistory) {
            $unwelcomeHistory = $unwelcomeHistory->jsonSerialize();
            foreach($unwelcomeHistory['items'] as &$item) {
                $isMy = $item->getClient() == $client;
                $item = $item->jsonSerialize();
                $item['isMy'] = $isMy;
            }
        }

        return new JsonResponse([
            'status' => true,
            'unwelcomeHistory' => $unwelcomeHistory
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
        $successDeleted = false;
        if ($tourist) {
            /** @var UnwelcomeHistory|null $unwelcomeHistory */
            $unwelcomeHistory = $this->getUnwelcomeRepository()->findOneByTourist($tourist);

            if($unwelcomeHistory) {
                foreach($unwelcomeHistory->getItems() as $unwelcome) {
                    if($unwelcome->getClient() == $client) {
                        $successDeleted = $unwelcomeHistory->removeItem($unwelcome);
                        break;
                    }
                }
                $this->dm->flush();
            }
        }

        return new JsonResponse([
            'status' => true,
            'successDeleted' => $successDeleted,
        ]);
    }

    /**
     * @Route("/has_unwelcome_history")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function hasUnwelcomeHistory()
    {
        $client = $this->getClient();

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