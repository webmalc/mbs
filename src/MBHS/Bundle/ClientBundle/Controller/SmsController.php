<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/sms")
 */
class SmsController extends Controller
{
    /**
     * Send client sms
     * @Route("/send")
     * @Method("GET")
     */
    public function sendAction(Request $request)
    {
        $mbhsRequest = $this->container->get('mbhs.request');
        $client = $mbhsRequest->getClient($request);

        if (empty($client)) {
            return new JsonResponse([
                    'error' => true,
                    'message' => 'Client not found',
                    'code' => 101
                ]);
        }

        $result = $mbhsRequest->sendSms($request, $client);

        if ($result->error) {
            return new JsonResponse([
                    'error' => true,
                    'message' => $result->message,
                    'code' => $result->code
                ]);
        }

        return new JsonResponse([
                'error' => false,
                'message' => 'Sms sent',
                'smsCount' => $client->getSmsCount()
            ]);
    }
}
