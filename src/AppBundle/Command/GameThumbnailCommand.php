<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 09.03.2016
 * Time: 23:08
 */

namespace AppBundle\Command;

use AppBundle\Entity\Game;
use AppBundle\Game\GameThumbnailGenerator;
use AppBundle\Repository\GameRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GameThumbnailCommand extends ContainerAwareCommand
{
    /**
     * @var GameRepository
     */
    private $gameRepository;
    /**
     * @var GameThumbnailGenerator
     */
    private $thumbnailGenerator;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        $name = null,
        GameRepository $gameRepository,
        GameThumbnailGenerator $thumbnailGenerator,
        LoggerInterface $logger
    ) {
        parent::__construct($name);
        $this->gameRepository = $gameRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('karopapier:game:thumbnail')
            ->setDescription('Create a game\'s preview image')
            ->addArgument(
                'gid',
                InputArgument::REQUIRED,
                'What is the GameId?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $gid = $input->getArgument('gid');
        /** @var Game $game */
        $game = $this->gameRepository->findGameWithPlayersAndMoves($gid);


        if (!$game) {
            $this->logger->error(sprintf('Game ID %s not found', $gid));

            return;
        }

        $this->thumbnailGenerator->generate($game);
        $this->logger->info('Thumbnail genertated for '.$game->getName());
    }

}
