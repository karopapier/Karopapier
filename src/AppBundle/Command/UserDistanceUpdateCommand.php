<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserDistanceUpdateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
                ->setName('karopapier:user:distanceupdate')
                ->setDescription('Calculate the distance driven for all or given user')
                ->addArgument(
                        'username',
                        InputArgument::REQUIRED,
                        'username to update'
                )
                ->addOption(
                        'all',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'all?',
                        false
                );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $logger = $container->get("logger");

        $username = $input->getArgument("username");
        $all = (bool)$input->getOption("all");
        $ur = $container->get("user_repository");
        if ($all) {
            $users = $ur->findAll();
        } else {
            $users = array(
                    $ur->findOneBy(array("login" => $username))
            );
        }


        $calc = $container->get('user_distance_calculator');
        /** @var User $user */
        foreach ($users as $user) {
            $logger->info(sprintf("Update distance for %s", $user->getUsername()));
            $distance = $calc->updateDistance($user);
        }
    }
}
