<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ChatImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
                ->setName('karopapier:chat:import')
                ->setDescription('Import the chat log into the database');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $chatService = $container->get("chat_service");
        $importer = $container->get("chatlog_importer");

        $path = $container->getParameter("chat")['logpath'];
        $importer->import($path);
    }
}
