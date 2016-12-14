<?php

namespace AppBundle\Command;

use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\User;
use AppBundle\Services\GameInconsistencyFinder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GameCheckCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
                ->setName('karopapier:game:check')
                ->setDescription('Check a game for consistency');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $finder = $container->get("game_inconsistency_finder");
        $finder->checkStartedWithoutPlayers();
        $finder->checkFinishedWithoutKaroMAMA();
    }
}