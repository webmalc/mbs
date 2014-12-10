<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController as Controller;
use MBHS\Bundle\ClientBundle\Document\PirateClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/login")
 */
class LoginController extends Controller
{
    /**
     * Set client last login
     * @Route("/")
     * @Method("GET")
     */
    public function loginAction(Request $request)
    {
        $dm = $this->container->get('doctrine_mongodb')->getManager();
        $mbhsRequest = $this->container->get('mbhs.request');
        $client = $mbhsRequest->getClient($request);

        if (empty($client)) {
            $mbhsRequest->addPirateClient($request);
        } else {
            $client->setLastLogin(new \DateTime());
            $dm->persist($client);
        }
        $dm->flush();

        return new Response();
    }
}
