<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController;
use MBHS\Bundle\ClientBundle\Document\Invite;
use MBHS\Bundle\ClientBundle\Document\Tourist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class InviteController
 * @Route("/invite")
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class InviteController extends BaseController
{
    private function getRequestData()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        return json_decode($request->getContent(), true);
    }

    protected function getRequestInvite()
    {
        $data = $this->getRequestData();
        $invite = null;
        if($data && isset($data['invite'])) {
            $data = $data['invite'];

            $guests = [];
            foreach((array)$data['guests'] as $guest) {
                $g = new Tourist();
                $g->setFirstName($guest['firstName']);
                $g->setLastName($guest['lastName']);
                $guests[] = $g;
            }

            $invite = new Invite();
            $invite
                ->setArrival($this->get('mbhs.helper')->getDateFromString($data['arrival']))
                ->setDeparture($this->get('mbhs.helper')->getDateFromString($data['departure']))
                //->setHotel($data['hotel'])
                ->setType($data['type'])
                ->setGuests($guests)
            ;
        }
        return $invite;
    }

    /**
     * @Route("/add")
     * @return JsonResponse
     */
    public function addAction()
    {
        $client = $this->getClient();

        $invite = $this->getRequestInvite();
        if($invite) {
            $this->dm->persist($invite);
            $this->dm->flush();
        }

        return new JsonResponse(['status' => true]);
    }

    private function getClient()
    {
        $client = $this->container->get('mbhs.request')->getClient($this->get('request_stack')->getCurrentRequest());
        if(!$client) {
            throw $this->createAccessDeniedException();
        }
        return $client;
    }
}