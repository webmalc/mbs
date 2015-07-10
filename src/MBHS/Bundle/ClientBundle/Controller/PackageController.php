<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController as Controller;
use MBHS\Bundle\BaseBundle\Document\Log;
use MBHS\Bundle\ClientBundle\Document\Package;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/package")
 */
class PackageController extends Controller
{

    /**
     * Add client channel manager package count
     * @Route("/channelmanager")
     * @Method("GET")
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function channelManagerAction(Request $request)
    {
        //TODO: Legacy action. Remove in future.
        return $this->redirect($this->generateUrl('client_package_log', $request->query->all()));
    }

    /**
     * Add client channel manager package count
     * @Route("/log", name="client_package_log")
     * @Method({"POST", "GET"})
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function logAction(Request $request)
    {
        $mbhsRequest = $this->container->get('mbhs.request');
        $client = $mbhsRequest->getClient($request);

        if (empty($client)) {
            $mbhsRequest->addPirateClient($request);
        } else {
            $mbhsRequest->addPackage($request, $client);
        }

        return new JsonResponse(['status' => true]);
    }
}
