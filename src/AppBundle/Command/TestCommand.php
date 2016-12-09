<?php

namespace AppBundle\Command;

use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
                ->setName('karopapier:test')
                ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getContainer();
        $chatService = $container->get("chat_service");

        $user = new User();
        $cm = new ChatMessage($user, "Bla");
        $chatService->addToRedis($cm);

    }
}
