<?php

namespace MBHS\Bundle\ClientBundle\Controller;

use MBHS\Bundle\BaseBundle\Controller\BaseController;
use MBHS\Bundle\ClientBundle\Document\Hotel;
use MBHS\Bundle\ClientBundle\Document\Invite;
use MBHS\Bundle\ClientBundle\Document\InvitedTourist;
use MBHS\Bundle\ClientBundle\Document\Tourist;
use MBHS\Bundle\ClientBundle\Document\TripRoute;
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
            $invite = new Invite();
            $data = $data['invite'];

            $invite
                ->setArrival($this->get('mbhs.helper')->getDateFromString($data['arrival']))
                ->setDeparture($this->get('mbhs.helper')->getDateFromString($data['departure']))
                ->setType($data['type'])
            ;

            $hotel = new Hotel();
            $hotel
                ->setTitle($data['hotel']['title'])
                ->setCity($data['hotel']['city'])
            ;
            $invite->setHotel($hotel);

            foreach((array)$data['tripRoutes'] as $route) {
                if(isset($route['hotel'])) {
                    $tripRoute = new TripRoute();
                    $tripRoute->setHotel($route['hotel']);
                    $tripRoute->setAddress($route['address']);
                    $invite->addTripRoute($tripRoute);
                }
            }

            foreach((array)$data['guests'] as $guest) {
                $g = new InvitedTourist();
                $g->setFirstName($guest['firstName'])
                    ->setLastName($guest['lastName'])
                    ->setBirthday($guest['birthDay'])
                    ->setBirthplace($guest['birthplace'])
                    ->setPassport($guest['passport'])
                    ->setSex($guest['sex']);
                $invite->addGuest($g);
            }
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