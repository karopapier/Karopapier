<?php

namespace AppBundle\Entity;

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
    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="U_ID", referencedColumnName="U_ID")
     */
    private $dranUser;


    /**
     * @var \AppBundle\Entity\Player
     * @ORM\OneToMany(targetEntity="Player", mappedBy="game")
     */
    private $players;

    /**
     * @return Player
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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Map")
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
     * @var integer
     *
     * @ORM\Column(name="startedby", type="integer", nullable=false)
     */
    private $startedby;

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
        return $this->finisheddate;
    }

    public function getCheckpointsEnabled()
    {
        if (!$this->map->getHasCheckpoints()) {
            return false;
        }

        return $this->checkpoints;
    }
}
