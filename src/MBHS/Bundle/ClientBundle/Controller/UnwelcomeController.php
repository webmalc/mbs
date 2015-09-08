<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController;
use MBHS\Bundle\ClientBundle\Document\Tourist;
use MBHS\Bundle\ClientBundle\Document\Unwelcome;
use MBHS\Bundle\ClientBundle\Document\UnwelcomeHistory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
                ->setBirthday($this->get('mbhs.helper')->getDateFromString($data['tourist']['birthday']))
                ->setEmail($data['tourist']['email'])
                ->setFirstName($data['tourist']['firstName'])
                ->setLastName($data['tourist']['lastName'])
                ->setPhone($data['tourist']['phone']);
        }
        return $tourist;
    }

    private function getRequestUnwelcome()
    {
        $data = $this->getRequestData();
        $unwelcome = null;
        if($data && isset($data['unwelcome'])) {
            $unwelcome = new Unwelcome();
            $unwelcome
                ->setClient($this->getClient())
                ->setComment($data['unwelcome']['comment'])
                ->setAggressor($data['unwelcome']['isAggressor']);
        }

        return $unwelcome;
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
                    'message' => 'Can not add new unwelcome. Unwelcome for this tourist is already exists.'
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
                    ->setComment($requestUnwelcome->getComment())
                    ->setAggressor($requestUnwelcome->getIsAggressor());

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
}