<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController as Controller;
use MBHS\Bundle\BaseBundle\Document\Log;
use MBHS\Bundle\BaseBundle\Lib\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/channelmanager")
 */
class ChannelManagerController extends Controller
{
    /**
     * Get push notifications
     * @Route("/push/{service}")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param string $service
     * @return JsonResponse
     */
    public function pushAction(Request $request, $service)
    {
        $ch = $this->get('mbhs.channelmanager');
        $response = null;
        $log = new Log();
        $log->setType('channelmanager');

        try {
            $client = $ch->getClient($service, $request);
            $response = $ch->sendPush($service, $request, $client);
            $log->setText('Push. Complete for client <' . $client . '>');

        } catch (Exception $e) {
            $log->setText('Push. ' . $e->getMessage());
        }

        $this->dm->persist($log);
        $this->dm->flush();

        if ($response) {
            return $response;
        }

        throw $this->createNotFoundException();
    }
}
