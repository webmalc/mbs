<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TouristController
 * @Route("/tourist")
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class TouristController
{
    /**
     * @Route("/add_black_list")
     * @Method("GET")
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addBlackListAction(Request $request)
    {
        

        return new JsonResponse(['status' => true]);
    }
}