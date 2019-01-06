<?php

namespace AppBundle\Modules\Karolenderblatt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Modules\Karolenderblatt\Service\KarolenderblattGenerator;

class KarolenderGenerationCommand extends Command
{
    /**
     * @var KarolenderblattGenerator
     */
    private $karolenderblattGenerator;

    public function __construct($name = null, KarolenderblattGenerator $karolenderblattGenerator)
    {
        parent::__construct($name);
        $this->karolenderblattGenerator = $karolenderblattGenerator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('karopapier:karolender:generate')
            ->setDescription('Recreate karolender entries from source');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->karolenderblattGenerator->generate();
    }
}
