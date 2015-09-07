<?php

namespace MBHS\Bundle\BaseBundle\Command;

use MBHS\Bundle\BaseBundle\Document\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UserCreateCommand
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class UserCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mbhs:base:create_user')
            ->setDescription('Creates a client')
            ->addArgument('username', InputArgument::REQUIRED, 'User name')
            ->addArgument('password', InputArgument::REQUIRED, 'password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $user->setUsername($input->getArgument('username'));
        $user->setUsernameCanonical($input->getArgument('username'));
        $user->setPassword(
            $this->getContainer()->get('security.password_encoder')
                ->encodePassword($user, $input->getArgument('password'))
        );

        $user->setRoles(['ROLE_ADMIN']);
        $user->setEnabled(true);

        $this->getContainer()->get('fos_user.user_manager')->updateUser($user);
        $output->writeln('Done');
    }
}