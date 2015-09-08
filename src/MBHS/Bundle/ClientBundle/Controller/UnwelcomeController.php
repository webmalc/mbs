<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController;
use MBHS\Bundle\ClientBundle\Document\Tourist;
use MBHS\Bundle\ClientBundle\Document\Unwelcome;
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
        if($data) {
            $unwelcome = new Unwelcome();
            $unwelcome
                ->setClient($this->getClient())
                ->setComment($data['comment'])
                ->setAggressor($data['isAggressor']);
        }

        return $unwelcome;
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
        $client = $this->container->get('mbhs.request')->getClient($this->get('request_stack')->getCurrentRequest());
        if(!$client) {
            throw $this->createAccessDeniedException();
        }
        return $client;
    }

    /**
     * @Route("/add")
     * @Method("POST")
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $client = $this->getClient();

        $requestUnwelcome = $this->getRequestUnwelcome();
        $requestTourist = $this->getRequestTourist();
        $requestUnwelcome->setTourist($requestTourist);

        if(!$requestUnwelcome || !$requestTourist) {
            return new JsonResponse(['status' => false]);
        }

        /** @var Unwelcome|null $unwelcome */
        $unwelcome = $this->getUnwelcomeRepository()->findOneByTourist($requestTourist);
        if($unwelcome) {
            return new JsonResponse(['status' => false]);
        }

        $this->dm->persist($requestUnwelcome);
        $this->dm->flush();

        return new JsonResponse(['status' => true]);
    }

    /**
     * @Route("/update")
     * @Method("POST")
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        $client = $this->getClient();

        $tourist = $this->getRequestTourist();

        $requestUnwelcome = $this->getRequestUnwelcome();
        /** @var Unwelcome|null $unwelcome */
        $unwelcome = $this->getUnwelcomeRepository()->findOneByTourist($tourist);

        if($requestUnwelcome && $unwelcome) {
            //if($blackListInfo->getClient() == $client) {}
            $unwelcome
                ->setComment($requestUnwelcome->getComment())
                ->setAggressor($requestUnwelcome->getIsAggressor());

            $this->dm->persist($unwelcome);
            $this->dm->flush();
        }

        return new JsonResponse(['status' => true]);
    }

    /**
     * @Route("/find_by_tourist")
     * @Method("POST")
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function findByTourist(Request $request)
    {
        $client = $this->getClient();
        $unwelcome = null;

        $tourist = $this->getRequestTourist();
        if ($tourist) {
            /** @var Unwelcome|null $unwelcome */
            $unwelcome = $this->getUnwelcomeRepository()->findOneByTourist($tourist);
        }

        return new JsonResponse([
            'status' => true,
            'blackListInfo' => $unwelcome
        ]);
    }

    /**
     * @Route("/delete_by_tourist")
     * @Method("POST")
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteByTourist(Request $request)
    {
        $client = $this->getClient();

        $tourist = $this->getRequestTourist();
        if ($tourist) {
            /** @var Unwelcome|null $unwelcome */
            $unwelcome = $this->getUnwelcomeRepository()->findOneByTourist($tourist);

            if($unwelcome) {
                $this->dm->remove($unwelcome);
                $this->dm->flush();
            }
        }

        return new JsonResponse([
            'status' => true
        ]);
    }
}