<?php

namespace AppBundle\Command;

use AppBundle\Chat\ChatlogImporter;
use AppBundle\Chat\ChatService;
use AppBundle\Services\ConfigService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChatImportCommand extends ContainerAwareCommand
{

    /**
     * @var ConfigService
     */
    private $configService;
    /**
     * @var ChatService
     */
    private $chatService;
    /**
     * @var ChatlogImporter
     */
    private $chatlogImporter;

    public function __construct(
        $name = null,
        ConfigService $configService,
        ChatService $chatService,
        ChatlogImporter $chatlogImporter
    ) {
        $this->configService = $configService;
        $this->chatService = $chatService;
        $this->chatlogImporter = $chatlogImporter;

        parent::__construct($name);
    }

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
        $path = $this->configService->get('chat.logpath');
        $this->chatlogImporter->import($path);
    }
}
