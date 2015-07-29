<?php

namespace MBHS\Bundle\ClientBundle\Service;

use Guzzle\Http\Exception\BadResponseException;
use MBHS\Bundle\BaseBundle\Lib\Exception;

use MBHS\Bundle\ClientBundle\Document\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

/**
 *  Calculation service
 */
class ChannelManager
{
    const PUSH_URL = '/management/channelmanager/package/notifications/';

    const SERVICES = [
      'vashotel', 'booking'
    ];

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->dm = $container->get('doctrine_mongodb')->getManager();
    }

    /**
     * @param $service
     * @param Request $request
     * @param Client $client
     * @return Response
     * @throws Exception
     */
    public function sendPush($service, Request $request, Client $client)
    {
        $url = $client->getUrl() . self::PUSH_URL . $service;

        $client = $this->container->get('guzzle.client');

        try {
            if ($request->getMethod() != 'GET') {
                $clientRequest = $client
                    ->post($url)
                    ->setBody($request->getContent())
                ;
            } else {
                $clientRequest = $client
                    ->get($url . '?' . $request->getQueryString());
            }

            $result = $clientRequest->setHeader('Content-Type', $request->getContentType())->send();

        } catch (BadResponseException $e) {
            throw new Exception($e->getMessage());
        }

        $response = new Response();
        $response->setContent((string) $result->getBody())
            ->headers
            ->set('Content-Type', (string) $result->getHeader('Content-Type'))
        ;

        return $response;
    }

    /**
     * @param $service
     * @param Request $request
     * @return \MBHS\Bundle\ClientBundle\Document\Client
     * @throws Exception
     */
    public function getClient($service, Request $request)
    {
        if (!in_array($service, self::SERVICES)) {
            throw new Exception('Unknown channel <' . $service .'>');
        }

        $method = 'getHotel' . ucfirst($service);
        if (!method_exists($this, $method)) {
            throw new Exception('Method <' . $method .'> do not exist in class <' . __CLASS__ . '>');
        }

        $hotel = $this->$method($request);

        $channel = $this->dm->getRepository('MBHSClientBundle:ChannelManager')->findOneBy([
            'key' => $hotel, 'title' => $service
        ]);

        if (!$channel) {
            throw new Exception('Client channelmanager not found. Service: <' . $service .'> . Hotel ID <' . $hotel . '>');
        }

        return $channel->getClient();
    }

    /**
     * @param Request $request
     * @return int
     * @throws Exception
     */
    private function getHotelVashotel(Request $request)
    {
        $xml = simplexml_load_string($request->getContent());

        $res = $xml->xpath('hotel_id');

        if (!count($res)) {
            throw new Exception('Invalid xml from Vashotel.ru');
        }
        return (int) $res[0];
    }

    /**
     * @param Request $request
     * @return int
     * @throws Exception
     */
    private function getHotelBooking(Request $request)
    {
        if (!$request->get('hotelid')) {
            throw new Exception('Invalid request from Booking.com');
        }

        return (int) $request->get('hotelid');
    }
}
