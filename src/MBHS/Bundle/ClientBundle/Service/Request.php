<?php

namespace MBHS\Bundle\ClientBundle\Service;

use MBHS\Bundle\BaseBundle\Document\Log;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use MBHS\Bundle\ClientBundle\Document\Client;

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
        if(!$request->get('url') || !$request->get('key')) {
            return null;
        }

        $client = $this->dm->getRepository('MBHSClientBundle:Client')->findOneBy([
                'url' => $request->get('url'),
                'ip'  => $request->getClientIp(),
                'key' => $request->get('key')
            ]);

        return $client;
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
        if(!$request->get('phone')) {
            $result->error = true;
            $result->message = 'phone parameter not found';
            $result->code = 103;

            return $result;
        }
        if($client->getSmsCount() <= -10) {
            $result->error = true;
            $result->message = 'sms count less then -10';
            $result->code = 104;

            return $result;
        }

        try {
            $text = $request->get('sms');
            (preg_match('/[а-яёА-ЯЁ]+/', $text)) ? $max = 70: $max = 140;
            $minus = ceil(mb_strlen($text)/$max);
            $this->container->get('mbhs.epochta')->send($request->get('sms'), $request->get('phone'));
        } catch (\Exception $e) {
            $result->error = true;
            $result->message = $e->getMessage();
            $result->code = 105;

            return $result;
        }

        $client->setSmsCount($client->getSmsCount() - $minus);
        $this->dm->persist($client);
        $this->dm->flush();

        // write log
        $log = new Log();
        $log->setType('sms')
            ->setClient($client)
            ->setText('Sms sent. Sms count: ' . $client->getSmsCount() . '. Text: ' . $request->get('sms') . '. Phone: ' . $request->get('phone'))
        ;
        $this->dm->persist($log);
        $this->dm->flush();

        return $result;
    }
}
