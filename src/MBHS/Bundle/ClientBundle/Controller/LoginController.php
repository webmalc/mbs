<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/login")
 */
class LoginController extends Controller
{
    /**
     * /**
     * Set client last login
     * @Route("/")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $mbhsRequest = $this->container->get('mbhs.request');
        $client = $mbhsRequest->getClient($request);

        if (empty($client)) {
            $mbhsRequest->addPirateClient($request);
        } else {
            $client->setLastLogin(new \DateTime());
            $this->dm->persist($client);
        }
        $this->dm->flush();

        return new JsonResponse(['status' => true]);
    }
}
