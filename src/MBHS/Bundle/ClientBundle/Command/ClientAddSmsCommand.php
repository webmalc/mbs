<?php

namespace MBHS\Bundle\ClientBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ClientAddSmsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('mbhs:client:sms')
            ->setDescription('Add sms to a client')
            ->addArgument('query', InputArgument::REQUIRED, 'Search query (title, url, email or Id)')
            ->addArgument('sms', InputArgument::REQUIRED, 'Sms count')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $query = trim($input->getArgument('query'));

        $qb = $dm->getRepository('MBHSClientBundle:Client')->createQueryBuilder('q');

        $client = $qb->addOr($qb->expr()->field('id')->equals($query))
            ->addOr($qb->expr()->field('title')->equals($query))
            ->addOr($qb->expr()->field('url')->equals($query))
            ->addOr($qb->expr()->field('email')->equals($query))
            ->limit(1)
            ->getQuery()
            ->getSingleResult();
        ;

        if(!$client) {
            $output->writeln('<error>Client not found! Query: ' . $input->getArgument('query') . '</error>');

            return false;
        }

        $client->setSmsCount($input->getArgument('sms'));
        $dm->persist($client);
        $dm->flush();

        $output->writeln('<info>Sms added. Client: ' . $client->getTitle() . ' (#' . $client->getId() . '). Sms count: ' . $client->getSmsCount() . '</info>');
    }
    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('query')) {
            $arg = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Please enter a search query (client title, url, email or Id):</question>',
                function($arg) {
                    if (empty($arg)) {
                        throw new \Exception('Query can not be empty');
                    }
                    return $arg;
                }
            );
            $input->setArgument('query', $arg);
            unset($arg);
        }
        if (!$input->getArgument('sms')) {
            $arg = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Please enter a sms count:</question>',
                function($arg) {
                    if (empty($arg)) {
                        throw new \Exception('Sms count can not be empty');
                    }

                    return $arg;
                }
            );
            $input->setArgument('sms', (int) $arg);
            unset($arg);
        }
    }
}