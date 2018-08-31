<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 08:52
 */

namespace AppBundle\Game;

use AppBundle\Entity\Game;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Psr\Log\LoggerInterface;

/**
 *
 * Find games with inconsistencies
 * @package AppBundle\Game
 */
class GameInconsistencyFinder
{
    /**
     * @var GameChecker
     */
    private $checker;

    /** @var EntityManager $em */
    private $em;

    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct(GameChecker $checker, ObjectManager $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->gr = $em->getRepository('AppBundle:Game');
        $this->ur = $em->getRepository('AppBundle:User');
        $this->pr = $em->getRepository('AppBundle:Player');
        $this->logger = $logger;
        $this->checker = $checker;
    }


    public function checkMamaDranButNotFinished()
    {
        $query = $this->em->createQuery(
            'SELECT g FROM AppBundle:Game g WHERE g.finished = FALSE AND g.dranUser = :uid ORDER BY g.id DESC'
        );
        $query->setParameter("uid", 26);
        /** @var Game $game */
        $games = $query->execute();
        foreach ($games as $game) {
            $this->logger->warning(
                sprintf(
                    "Game %s shows KaroMAMA dran but is not finished",
                    $game->getId()." - ".$game->getName()
                )
            );
            $this->checker->ensureFinished($game);
        }
    }

    public function checkFinishedWithoutKaroMAMA()
    {
        $query = $this->em->createQuery(
            'SELECT g FROM AppBundle:Game g WHERE g.finished = TRUE AND g.dranUser != :uid ORDER BY g.id DESC'
        );
        $query->setParameter("uid", 26);
        /** @var Game $game */
        $games = $query->execute();

        foreach ($games as $game) {
            if ($game->isFinished()) {
                $dran = $game->getDranUser();
                if ($dran->getId() != 26) {
                    $this->logger->warning(
                        sprintf(
                            "Game %s shows user %s dran but is finished, need to update to Mama",
                            $game->getId()." - ".$game->getName(),
                            $dran->getId()
                        )
                    );
                    $this->checker->ensureFinished($game);
                }
            }
        }
    }

    public function checkStartedWithoutPlayers()
    {
        /*
        SELECT g.G_ID, count(p.U_ID) as num FROM `karo_games` as g left join karo_teilnehmer as p on g.G_ID=p.G_ID where g.G_ID >= 94760 group by g.G_ID order by g.G_ID asc
        ->
        SELECT g.G_ID, count(p.U_ID) as num FROM `karo_games` as g left join karo_teilnehmer as p on g.G_ID=p.G_ID where g.G_ID >= 94760 group by g.G_ID having num=0 order by g.G_ID asc
        */

        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata('AppBundle:Game', 'g');
        $rsm->addFieldResult('g', 'G_ID', 'id');

        //"SELECT g.G_ID, count(p.U_ID) as num FROM `karo_games` as g left join karo_teilnehmer as p on g.G_ID=p.G_ID where g.G_ID >= 94760 group by g.G_ID having num=0 order by g.G_ID asc ",
        $sql = "SELECT g.G_ID, count(p.U_ID) AS num FROM `karo_games` AS g LEFT JOIN karo_teilnehmer AS p ON g.G_ID=p.G_ID WHERE g.U_ID != 26 GROUP BY g.G_ID HAVING num=0 ORDER BY g.G_ID ASC";
        $query = $this->em->createNativeQuery(
            $sql,
            $rsm
        );
        /** @var Game $game */

        $games = $query->execute();
        foreach ($games as $game) {
            $this->logger->info("Game started without players: ".$game->getId());
            $this->checker->ensureFinished($game);
        }

    }

    public function checkDranNotActive()
    {
        /*
         *  SELECT karo_games.G_ID, name, karo_teilnehmer.U_ID, status FROM `karo_games` inner join `karo_teilnehmer` on karo_games.G_ID=karo_teilnehmer.G_ID where karo_games.U_ID=karo_teilnehmer.U_ID and status<1
         */
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata('AppBundle:Game', 'g');
        $rsm->addFieldResult('g', 'G_ID', 'id');

        $sql = "SELECT karo_games.G_ID, name, karo_teilnehmer.U_ID, status FROM `karo_games` INNER JOIN `karo_teilnehmer` ON karo_games.G_ID=karo_teilnehmer.G_ID WHERE karo_games.U_ID=karo_teilnehmer.U_ID AND status<1";
        $query = $this->em->createNativeQuery(
            $sql,
            $rsm
        );
        $games = $query->execute();
        /** @var Game $game */
        foreach ($games as $game) {
            $this->logger->info("Game dran user not active: ".$game->getId());
            $this->checker->ensureFinished($game);
        }
    }

    public function deletePlayingMama()
    {
        $nativeConnection = $this->em->getConnection();
        $sql = "DELETE FROM karo_teilnehmer WHERE U_ID=26";
        $nativeConnection->executeUpdate($sql);
    }
}
