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
    /**
     * @Route("/add")
     * @Method("POST")
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $client = $this->getClient();

        $data = json_decode($request->getContent(), true);
        $unwelcome = new Unwelcome();
        $unwelcome
            ->setClient($client)
            ->setComment($data['comment'])
            ->setAggressor($data['isAggressor']);

        $tourist = new Tourist();
        $tourist
            ->setBirthday($data['tourist']['birthday'])
            ->setEmail($data['tourist']['email'])
            ->setFirstName($data['tourist']['firstName'])
            ->setLastName($data['tourist']['lastName'])
            ->setPhone($data['tourist']['phone']);

        $unwelcome->setTourist($tourist);

        $this->dm->persist($unwelcome);
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
        $data = json_decode($request->getContent(), true);
        $client = $this->getClient();

        $birthday = $data['tourist']['birthday'];
        $firstName = $data['tourist']['firstName'];
        $lastName = $data['tourist']['lastName'];

        /** @var Unwelcome|null $unwelcome */
        $unwelcome = $this->getUnwelcomeRepository()->findOneBy([
            'tourist.firstName' => $firstName,
            'tourist.lastName' => $lastName,
            'tourist.birthday' => $birthday,
        ]);

        if($unwelcome) {
            //if($blackListInfo->getClient() == $client) {}
            $unwelcome
                ->setComment($data['comment'])
                ->setAggressor($data['isAggressor']);

            $this->dm->persist($unwelcome);
            $this->dm->flush();
        }

        return new JsonResponse(['status' => true]);
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
     * @Route("/find_by_tourist")
     * @Method("POST")
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function findByTourist(Request $request)
    {
        $client = $this->getClient();
        $unwelcome = null;

        $data = json_decode($request->getContent(), true);
        if ($data) {
            /** @var Unwelcome|null $unwelcome */
            $unwelcome = $this->getUnwelcomeRepository()->findOneBy([
                'tourist.firstName' => $data['tourist']['firstName'],
                'tourist.lastName' => $data['tourist']['lastName'],
                'tourist.birthday' => $data['tourist']['birthday'],
            ]);
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

        $data = json_decode($request->getContent(), true);
        if ($data) {
            /** @var Unwelcome|null $unwelcome */
            $unwelcome = $this->getUnwelcomeRepository()->findOneBy([
                'tourist.firstName' => $data['tourist']['firstName'],
                'tourist.lastName' => $data['tourist']['lastName'],
                'tourist.birthday' => $data['tourist']['birthday'],
            ]);
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