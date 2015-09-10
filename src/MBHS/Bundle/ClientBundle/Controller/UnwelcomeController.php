<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController;
use MBHS\Bundle\ClientBundle\Document\Hotel;
use MBHS\Bundle\ClientBundle\Document\Tourist;
use MBHS\Bundle\ClientBundle\Document\Unwelcome;
use MBHS\Bundle\ClientBundle\Document\UnwelcomeHistory;
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
        return json_decode($request->getContent(), true);
    }

    /**
     * @return Tourist|null
     */
    private function getRequestTourist()
    {
        $data = $this->getRequestData();
        $tourist = null;
        if($data && isset($data['tourist'])) {
            $tourist = new Tourist();
            $tourist
                ->setFirstName($data['tourist']['firstName'])
                ->setLastName($data['tourist']['lastName'])
                ->setBirthday($this->get('mbhs.helper')->getDateFromString($data['tourist']['birthday']))
                ->setEmail($data['tourist']['email'])
                ->setPhone($data['tourist']['phone']);
        }
        return $tourist;
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
     * @return \MBHS\Bundle\ClientBundle\Document\UnwelcomeHistoryRepository
     */
    private function getUnwelcomeHistoryRepository()
    {
        return $this->dm->getRepository('MBHSClientBundle:UnwelcomeHistory');
    }

    /**
     * @return \MBHS\Bundle\ClientBundle\Document\Client|null
     */
    private function getClient()
    {
        $client = $this->container->get('mbhs.request')->getClient($this->get('request_stack')->getCurrentRequest());
        if(!$client) {
            throw $this->createAccessDeniedException();
        }
        return $client;
    }

    /**
     * @Route("/add")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction()
    {
        $client = $this->getClient();

        $requestUnwelcome = $this->getRequestUnwelcome();
        $requestTourist = $this->getRequestTourist();

        if(!$requestUnwelcome || !$requestTourist) {
            return new JsonResponse(['status' => false]);
        }

        $unwelcome = null;
        $unwelcomeHistory = $this->getUnwelcomeHistoryRepository()->findOneByTourist($requestTourist);
        if ($unwelcomeHistory) {
            foreach ($unwelcomeHistory->getItems() as $u) {
                if ($u == $client) {
                    $unwelcome = $u;
                }
            }

            if($unwelcome) {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'Can not add new unwelcome. Unwelcome for this tourist already exists.'
                ]);
            }
        } else {
            $unwelcomeHistory = new UnwelcomeHistory();
            $unwelcomeHistory->setTourist($requestTourist);
        }

        $unwelcomeHistory->addItem($requestUnwelcome);
        $this->dm->persist($unwelcomeHistory);
        //$this->dm->persist($requestUnwelcome);
        $this->dm->flush();

        return new JsonResponse(['status' => true]);
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
        $unwelcomeHistory = $this->getUnwelcomeHistoryRepository()->findOneByTourist($requestTourist);
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
            /** @var UnwelcomeHistory|null $unwelcomeHistory */
            $unwelcomeHistory = $this->getUnwelcomeHistoryRepository()->findOneByTourist($requestTourist);
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
            $unwelcomeHistory = $this->getUnwelcomeHistoryRepository()->findOneByTourist($tourist);

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
                'result' => $this->getUnwelcomeHistoryRepository()->isUnwelcome($requestTourist)
            ]);
        }
        return new JsonResponse([
            'status' => false,
            'error' => 'Expect request with tourist'
        ]);
    }
}