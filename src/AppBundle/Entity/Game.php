<?php

namespace AppBundle\Entity;

use AppBundle\Model\PositionCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 *
 * @ORM\Table(name="karo_games", indexes={@ORM\Index(name="U_ID", columns={"U_ID"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * @var integer
     *
     * @ORM\Column(name="G_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="U_ID", referencedColumnName="U_ID")
     */
    private $dranUser;


    /**
     * @var \AppBundle\Entity\Player[]
     * @ORM\OneToMany(targetEntity="Player", mappedBy="game", fetch="EAGER")
     */
    private $players;

    /**
     * @return Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="Session", type="string", length=255, nullable=true)
     */
    private $session;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var \AppBundle\Entity\Map
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Map", fetch="EAGER")
     * @ORM\JoinColumn(name="M_ID", referencedColumnName="M_ID", nullable=false)
     */
    private $map;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mailsent", type="boolean", nullable=false)
     */
    private $mailsent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datemailsent", type="datetime", nullable=false)
     */
    private $datemailsent;

    /**
     * @var integer
     *
     * @ORM\Column(name="freeslots", type="integer", nullable=false)
     */
    private $freeslots;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=true)
     */
    private $comment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="started", type="boolean", nullable=false)
     */
    private $started;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="starteddate", type="datetime", nullable=false)
     */
    private $starteddate;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="startedby", referencedColumnName="U_ID")
     */
    private $startedBy;

    /**
     * @var boolean
     *
     * @ORM\Column(name="finished", type="boolean", nullable=false)
     */
    private $finished;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finisheddate", type="datetime", nullable=false)
     */
    private $finisheddate;

    /**
     * @var integer
     *
     * @ORM\Column(name="zzz", type="integer", nullable=false)
     */
    private $zzz;

    /**
     * @var integer
     *
     * @ORM\Column(name="checkpoints", type="integer", nullable=false)
     */
    private $checkpoints;

    /**
     * @var integer
     *
     * @ORM\Column(name="crashallowed", type="integer", nullable=false)
     */
    private $crashallowed;

    /**
     * @var integer
     *
     * @ORM\Column(name="startdirection", type="integer", nullable=false)
     */
    private $startdirection;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_archived", type="boolean", nullable=false)
     */
    private $isArchived;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getZzz()
    {
        return $this->zzz;
    }

    /**
     * @param int $zzz
     */
    public function setZzz($zzz)
    {
        $this->zzz = $zzz;
    }

    public function getDranUser()
    {
        return $this->dranUser;
    }

    public function getMap()
    {
        return $this->map;
    }

    public function getDetailsArray()
    {
        $data = [
            'started' => (bool)$this->started,
            'starteddate' => $this->starteddate->format('Y-m-d H:i:s'),
            'creator' => $this->startedBy->getName(),
            'finished' => (bool)$this->finished,
        ];

        if ($data['finished']) {
            $data['finisheddate'] = $this->getFinishedDate()->format('Y-m-d H:i:s');
        } else {
            // dran
            $data['next'] = [
                'id' => $this->dranUser->getId(),
                'name' => $this->dranUser->getName(),
            ];
            // blocked
            $diff = (time() - $this->datemailsent->format('U'));
            $data['blocked'] = floor($diff / 86400);
        }

        // time since last move

        return $data;
    }

    /**
     * Mark the game as finished, with optional timestamp and final user (KaroMAMA id 26)
     * @param int $ts
     * @param User $user
     */
    public function finish(\DateTimeInterface $fd = null, User $user)
    {
        if ($fd === null) {
            $fd = new \DateTime("now");
        }
        $this->finisheddate = $fd;
        $this->datemailsent = $fd;
        $this->dranUser = $user;
        $this->finished = true;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return (bool)$this->finished;
    }

    /**
     * @return \DateTime
     */
    public function getFinishedDate()
    {
        if ($this->finisheddate->format('Y') > 0) {
            return $this->finisheddate;
        }
    }

    public function getCheckpointsEnabled()
    {
        if (!$this->map->getHasCheckpoints()) {
            return false;
        }

        return $this->checkpoints;
    }

    public function getCrashAllowed()
    {
        $meanings = [
            'free',
            'allowed',
            'forbidden',
        ];

        return $meanings[$this->crashallowed];
    }

    public function getStartDirection()
    {
        $meanings = [
            'free',
            'classic',
            'formula1',
        ];

        return $meanings[$this->startdirection];

    }

    public function getBlockedPlayersPositions()
    {
        $players = $this->getPlayers();
        $positions = new PositionCollection();
        foreach ($players as $player) {
            // skip those that have NOT moved yet (game 75000 rule change)
            if ($player->hasMoved()) {
                $motion = $player->getCurrentMotion();
                $positions->add($motion->getPosition());
            }
        }

        return $positions;
    }

    public function getNextUser()
    {
        return $this->dranUser;
    }

    public function getNextPlayer()
    {
        foreach ($this->players as $player) {
            if ($player->getUser()->getId() === $this->dranUser->getId()) {
                return $player;
            }
        }
    }
}
