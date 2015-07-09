<?php

namespace MBHS\Bundle\ClientBundle\Command;

use MBHS\Bundle\ClientBundle\Document\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ClientCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mbhs:client:create')
            ->setDescription('Creates a client')
            ->addArgument('title', InputArgument::REQUIRED, 'Client title')
            ->addArgument('email', InputArgument::REQUIRED, 'Client email')
            ->addArgument('phone', InputArgument::REQUIRED, 'Client phone')
            ->addArgument('url', InputArgument::REQUIRED, 'Client url')
            ->addArgument('ip', InputArgument::REQUIRED, 'Client ip')
            ->addArgument('key', InputArgument::OPTIONAL, 'Client key')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $client->setTitle($input->getArgument('title'))
            ->setEmail($input->getArgument('email'))
            ->setPhone($input->getArgument('phone'))
            ->setUrl($input->getArgument('url'))
            ->setIp($input->getArgument('ip'))
        ;
        $key = $input->getArgument('key');
        if(empty($key)) {
            $key = $this->getContainer()->get('mbhs.helper')->getRandomString(40);
        }
        $client->setKey($key);

        $errors = $this->getContainer()->get('validator')->validate($client);

        if(count($errors)) {
            $output->writeln('<error>Errors occurred! Client not created.</error>');
            foreach($errors as $error) {
                $output->writeln('<error>' . $error->getPropertyPath() . ': ' .$error->getMessage() . '</error>');
            }
        } else {
            $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
            $dm->persist($client);
            $dm->flush();
            $output->writeln('<info>Client created. Id: ' . $client->getId() . '. Key: '. $client->getKey(). '</info>');
        }
    }
    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('title')) {
            $arg = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Please enter a title:</question>',
                function($arg) {
                    if (empty($arg)) {
                        throw new \Exception('Title can not be empty');
                    }

                    return $arg;
                }
            );
            $input->setArgument('title', $arg);
            unset($arg);
        }
        if (!$input->getArgument('email')) {
            $arg = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Please enter a email:</question>',
                function($arg) {
                    if (empty($arg)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $arg;
                }
            );
            $input->setArgument('email', $arg);
            unset($arg);
        }
        if (!$input->getArgument('phone')) {
            $arg = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Please enter a phone:</question>',
                function($arg) {
                    if (empty($arg)) {
                        throw new \Exception('Phone can not be empty');
                    }

                    return $arg;
                }
            );
            $input->setArgument('phone', $arg);
            unset($arg);
        }
        if (!$input->getArgument('url')) {
            $arg = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Please enter a url:</question>',
                function($arg) {
                    if (empty($arg)) {
                        throw new \Exception('Url can not be empty');
                    }

                    return $arg;
                }
            );
            $input->setArgument('url', $arg);
            unset($arg);
        }
        if (!$input->getArgument('ip')) {
            $arg = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Please enter a ip:</question>',
                function($arg) {
                    if (empty($arg)) {
                        throw new \Exception('Ip can not be empty');
                    }

                    return $arg;
                }
            );
            $input->setArgument('ip', $arg);
            unset($arg);
        }
        if (!$input->getArgument('key')) {
            $arg = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Please enter a key:</question>',
                function($arg) {
                    return $arg;
                }
            );
            $input->setArgument('key', $arg);
            unset($arg);
        }
    }
}