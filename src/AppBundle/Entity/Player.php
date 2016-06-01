<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="karo_teilnehmer", indexes={@ORM\Index(name="U_ID", columns={"U_ID"})})
 * @ORM\Entity
 */
class Player
{
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="moved", type="boolean", nullable=false)
     */
    private $moved;

    /**
     * @var boolean
     *
     * @ORM\Column(name="finished", type="boolean", nullable=false)
     */
    private $finished;

    /**
     * @var Game
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Game")
     * @ORM\JoinColumn(name="G_ID", referencedColumnName="G_ID")
     */
    private $game;

    /**
     * @var User
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="U_ID", referencedColumnName="U_ID")
     */
    private $user;

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
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
}
