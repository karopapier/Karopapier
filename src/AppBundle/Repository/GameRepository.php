<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Game;
use AppBundle\Entity\Player;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GameRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findAllOrderedByName()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM AppBundle:Game p ORDER BY p.name ASC'
            )
            ->getResult();
    }

    public function getDranGames(User $user)
    {
        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->createQueryBuilder();
        $qb->select(
            'G_ID as id, M_ID as mapId, name, datediff(now(),datemailsent) as blocked FROM karo_games'
        );
        $qb->where('U_ID='.$user->getId());
        $qb->orderBy('datemailsent', 'desc');
        $this->isActive($qb);
        $qb->setParameter('user', $user);

        $stmt = $qb->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isActive(QueryBuilder $qb)
    {
        return $this->isStarted($this->isNotFinished($qb));
    }

    public function isFinished()
    {

    }

    /**
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function isStarted(QueryBuilder $qb)
    {
        $qb->andWhere('started=1');

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function isNotFinished(QueryBuilder $qb)
    {
        $qb->andWhere('finished=0');

        return $qb;
    }


    /**
     * @param $gid
     * @return Game
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findGameWithPlayers($gid)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('g', 'p')
            ->from(Game::class, 'g')
            ->leftJoin('g.players', 'p')
            ->where('g.id = :gid');

        $qb->setParameter('gid', $gid);

        return $qb->getQuery()->getSingleResult();
    }

    public function addMovesData(Game $game)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb
            ->select('*')
            ->from('karo_moves')
            ->where('G_ID=:gid')
            ->orderBy('date');
        $qb->setParameter('gid', $game->getId());
        $stmt = $qb->execute();

        $allMoves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Init empty move array per player
        $players = $game->getPlayers();
        $playersMoves = [];
        /** @var Player $player */
        foreach ($players as $player) {
            $playersMoves[$player->getUser()->getId()] = [];
        }

        foreach ($allMoves as $move) {
            $uid = $move['U_ID'];
            $data = [
                'x' => $move['x_pos'],
                'y' => $move['y_pos'],
                'xv' => $move['x_vec'],
                'yv' => $move['y_vec'],
                't' => $move['date'],
            ];

            if ($move['movemessage'] !== '') {
                $data['msg'] = $move['movemessage'];
            }

            if ($move['crash'] !== '0') {
                $data['crash'] = 1;
            }
            $playersMoves[$uid][] = $data;
        }

        foreach ($players as $player) {
            $player->setMovesArray($playersMoves[$player->getUser()->getId()]);
        }
    }

    public function addCheckpointData(Game $game)
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb
            ->select('U_ID as uid,Checkpoint as cp')
            ->from('karo_checkpoints')
            ->where('G_ID=:gid')
            ->orderBy('checkpoint');
        $qb->setParameter('gid', $game->getId());
        $stmt = $qb->execute();

        $allCheckpoints = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('content-type: text/plain');

        // Init empty move array per player
        $players = $game->getPlayers();
        $playersCps = [];
        /** @var Player $player */
        foreach ($players as $player) {
            $playersCps[$player->getUser()->getId()] = [];
        }

        foreach ($allCheckpoints as $cp) {
            $playersCps[$cp['uid']][] = (int)$cp['cp'];
        }

        foreach ($players as $player) {
            $player->setCheckpointsArray($playersCps[$player->getUser()->getId()]);
        }
    }
}