<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController as Controller;
use MBHS\Bundle\BaseBundle\Document\Log;
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
     */
    public function channelManagerAction(Request $request)
    {
        $mbhsRequest = $this->container->get('mbhs.request');
        $client = $mbhsRequest->getClient($request);

        if (empty($client)) {
            $mbhsRequest->addPirateClient($request);
        } else {
            $dm = $this->container->get('doctrine_mongodb')->getManager();
            $client->setChannelManagerCount($client->getChannelManagerCount() + 1);
            $dm->persist($client);
            $dm->flush();

            $log = new Log();

            $text = 'Add channel manager package. Channel manager count: ' . $client->getChannelManagerCount() .
                '. Info: ' . $request->get('number') . ';' . $request->get('roomType') . ';' .
                $request->get('begin') . '-' . $request->get('end') . ';' .
                $request->get('tourist') . ';' . $request->get('tourist_phone') . ';' . $request->get('tourist_email') .
                ';' . $request->get('service')
            ;

            $log->setType('channelmanager')
                ->setClient($client)
                ->setText($text)
            ;
            $dm->persist($log);
            $dm->flush();
        }

        return new JsonResponse(['status' => true]);
    }
}
