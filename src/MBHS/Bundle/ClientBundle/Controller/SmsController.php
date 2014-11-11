<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/sms")
 */
class SmsController extends Controller
{
    /**
     * @Route("/send")
     * @Template()
     */
    public function sendAction()
    {
        $client = $this->get('guzzle.client');
        $request = $client->get('http://www.google.com/');
        echo $request->send()->getBody(); exit();

        return [];
    }
}
