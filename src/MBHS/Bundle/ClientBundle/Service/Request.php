<?php

namespace MBHS\Bundle\ClientBundle\Service;

use MBHS\Bundle\BaseBundle\Document\Log;
use MBHS\Bundle\ClientBundle\Document\Package;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use MBHS\Bundle\ClientBundle\Document\Client;
use MBHS\Bundle\ClientBundle\Document\PirateClient;

/**
 *  Calculation service
 */
class Request
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\Bundle\MongoDBBundle\ManagerRegistry
     */
    protected $dm;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->dm = $container->get('doctrine_mongodb')->getManager();
    }

    /**
     * @param SymfonyRequest $request
     * @return null|Client
     */
    public function getClient(SymfonyRequest $request)
    {
        $url = $request->get('url');
        $key = $request->get('key');

        if (!$url || !$key) {
            $data = json_decode($request->getContent());
            if ($data && !empty($data->key) && !empty($data->url)) {
                $url = $data->url;
                $key = $data->key;
            } else {
                return null;
            }
        }

        $client = $this->dm->getRepository('MBHSClientBundle:Client')->findOneBy([
                'url' => $url,
                'ip'  => $request->getClientIp(),
                'key' => $key
            ]);

        return $client;
    }

    /**
     * Add Package log entry
     * @param SymfonyRequest $request
     * @param $client
     * @return Package
     */
    public function addPackage(SymfonyRequest $request, $client)
    {
        $data = json_decode($request->getContent());
        $package = new Package();
        $package->setClient($client);

        if ($data) {
            $package
                ->setType(!empty($data->type) ? $data->type : null)
                ->setKey(!empty($data->packageKey) ? $data->packageKey : null)
                ->setNumber(!empty($data->number) ? $data->number : null)
                ->setHotel(!empty($data->hotel) ? $data->hotel : null)
                ->setRoomType(!empty($data->roomType) ? $data->roomType : null)
                ->setPayer(!empty($data->payer) ? $data->payer : null)
                ->setPrice(!empty($data->price) ? (float) $data->price : null)
                ->setBegin(
                    !empty($data->begin) ? \DateTime::CreateFromFormat("d.m.Y H:i:s", $data->begin ." 00:00:00") : null
                )
                ->setEnd(
                    !empty($data->end) ? \DateTime::CreateFromFormat("d.m.Y H:i:s", $data->end ." 00:00:00") : null
                )
            ;
        } else {
            $package->setType('channel_manager');
        }

        $this->dm->persist($package);
        $this->dm->flush();

        return $package;
    }

    /**
     * @param SymfonyRequest $request
     * @return PirateClient|boolean
     */
    public function addPirateClient(SymfonyRequest $request)
    {
        if (in_array($request->getClientIp(), ['176.192.20.30', '95.85.3.188'])) {
            return false;
        }

        $ip = $request->get('ip');
        $url = $request->get('url');
        $data = json_decode($request->getContent());

        if (!$url && !empty($data) && !empty($data->url)) {
            $url = $data->url;
        }
        if (!$ip && !empty($data) && !empty($data->ip)) {
            $ip = $data->ip;
        }

        $pirate = new PirateClient();
        $pirate->setServerIp($request->getClientIp())
            ->setUserIp($ip)
            ->setUrl($url)
        ;
        $this->dm->persist($pirate);
        $this->dm->flush();

        return $pirate;
    }

    public function sendSms(SymfonyRequest $request, Client $client)
    {
        $result = new \StdClass();
        $result->error = false;

        if(!$request->get('sms')) {
            $result->error = true;
            $result->message = 'sms parameter not found';
            $result->code = 102;

            return $result;
        }

        $phone = $request->get('phone');
        if(!$phone) {
            $result->error = true;
            $result->message = 'phone parameter not found';
            $result->code = 103;

            return $result;
        }
        $phone = $this->container->get('mbhs.helper')->cleanPhone($phone);

        if (mb_strlen($phone) !== 11) {
            $result->error = true;
            $result->message = 'invalid phone number: ' . $request->get('phone');
            $result->code = 104;

            return $result;
        }

        /*if($client->getSmsCount() <= -10) {
            $result->error = true;
            $result->message = 'sms count less then -10';
            $result->code = 105;

            return $result;
        }*/

        try {
            $text = $request->get('sms');
            (preg_match('/[а-яёА-ЯЁ]+/', $text)) ? $max = 70: $max = 140;
            $minus = ceil(mb_strlen($text)/$max);
            //$this->container->get('mbhs.epochta')->send($request->get('sms'), $phone);
        } catch (\Exception $e) {
            $result->error = true;
            $result->message = $e->getMessage();
            $result->code = 106;

            return $result;
        }

        /*$client->setSmsCount($client->getSmsCount() + $minus);
        $this->dm->persist($client);
        $this->dm->flush();*/

        // write log
        $log = new Log();
        $log->setType('sms')
            ->setClient($client)
            ->setText('Sms sent. Text: ' . $request->get('sms') . '. Phone: ' . $request->get('phone'))
        ;
        $this->dm->persist($log);
        $this->dm->flush();

        return $result;
    }
}
