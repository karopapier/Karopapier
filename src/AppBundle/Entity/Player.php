<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="karo_teilnehmer", indexes={@ORM\Index(name="U_ID", columns={"U_ID"})})
 * @ORM\Entity
 */
class Player
{
    public function __construct()
    {
        $this->checkpoints = new ArrayCollection();
        $this->moves = new ArrayCollection();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;


    /**
     * @var Move[]
     * @ORM\OneToMany(targetEntity="Move", mappedBy="player")
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $moves;


    /**
     * @var boolean
     *
     * @ORM\Column(name="moved", type="boolean", nullable=false)
     */
    private $moved;

    /**
     * @var integer
     *
     * @ORM\Column(name="finished", type="integer", nullable=false)
     */
    private $finished;

    /**
     * @var Game
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="players")
     * @ORM\JoinColumn(name="G_ID", referencedColumnName="G_ID")
     */
    private $game;

    /**
     * @var User
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="U_ID", referencedColumnName="U_ID")
     */
    private $user;

    /**
     * @var Checkpoint[]
     * @ORM\OneToMany(targetEntity="Checkpoint", mappedBy="player")
     * // DONT USE EAGER!
     */
    private $checkpoints;

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function isFinished()
    {
        return ($this->finished != 0);
    }

    public function getFinished()
    {
        return $this->finished;

    }

    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function __toString()
    {
        return $this->user->getLogin();
    }

    /**
     * @return Move[]|ArrayCollection
     */
    public function getMoves()
    {
        return $this->moves;
    }

    /**
     * @return Move
     */
    public function getLastMove()
    {
        return $this->moves->last();
    }

    public function isActive()
    {
        return (bool)$this->status == 1;
    }

    /**
     * @return bool
     */
    public function hasMoved()
    {
        return $this->moved;
    }

    /**
     * @return Checkpoint[]|ArrayCollection
     */
    public function getCheckpoints()
    {
        $plain = [];
        /** @var Checkpoint $checkpoint */
        foreach ($this->checkpoints as $checkpoint) {
            $plain[] = $checkpoint->getCheckpoint();
        }

        return $plain;
    }
}
