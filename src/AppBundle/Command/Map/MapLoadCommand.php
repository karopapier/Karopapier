<?php

namespace AppBundle\Command\Map;

use AppBundle\Map\MapLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MapLoadCommand extends ContainerAwareCommand
{
    /**
     * @var MapLoader
     */
    private $mapLoader;

    public function __construct($name = null, MapLoader $mapLoader)
    {
        parent::__construct($name);
        $this->mapLoader = $mapLoader;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('karopapier:map:load')
            ->setDescription('Load all official KaroMaps from *.map files');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->mapLoader->loadMapFolder();
    }
}
