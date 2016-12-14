<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Model\Motion;
use AppBundle\Model\Position;

/**
 * Move
 *
 * @ORM\Table(name="karo_moves", indexes={@ORM\Index(name="U_ID", columns={"U_ID"}), @ORM\Index(name="G_U_ID", columns={"G_ID", "U_ID"}), @ORM\Index(name="idx_date", columns={"date"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MoveRepository")
 */
class Move
{
    /**
     * @var Game
     * @ORM\OneToOne(targetEntity="Game")
     * @ORM\JoinColumn(name="G_ID", referencedColumnName="G_ID")
     */
    private $game;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="U_ID", referencedColumnName="U_ID")
     */
    private $user;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="moves")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="G_ID", referencedColumnName="G_ID"),
     *   @ORM\JoinColumn(name="U_ID", referencedColumnName="U_ID")
     * })
     */
    private $player;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="x_pos", type="smallint", nullable=false)
     */
    private $xPos;

    /**
     * @var integer
     *
     * @ORM\Column(name="y_pos", type="smallint", nullable=false)
     */
    private $yPos;

    /**
     * @var integer
     *
     * @ORM\Column(name="x_vec", type="smallint", nullable=false)
     */
    private $xVec;

    /**
     * @var integer
     *
     * @ORM\Column(name="y_vec", type="smallint", nullable=false)
     */
    private $yVec;

    /**
     * @var boolean
     *
     * @ORM\Column(name="crash", type="boolean", nullable=false)
     */
    private $crash;

    /**
     * @var string
     *
     * @ORM\Column(name="movemessage", type="string", length=255, nullable=false)
     */
    private $movemessage;

    /**
     * @var integer
     *
     * @ORM\Column(name="M_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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







    /**
     * @return int
     */
    public function getXPos()
    {
        return $this->xPos;
    }

    /**
     * @return integer
     */
    public function getYPos()
    {
        return $this->yPos;
    }

    /**
     * @return integer
     */
    public function getXVec()
    {
        return $this->xVec;
    }

    /**
     * @return integer
     */
    public function getYVec()
    {
        return $this->yVec;
    }

    /**
     * @return string
     */
    public function getMovemessage()
    {
        return $this->movemessage;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    public function __toString()
    {
        return $this->getXPos() . "|" . $this->getYPos() . " (" . $this->getXVec() . "|" . $this->getYVec() . ")";
    }

    public function setMotion(Motion $m)
    {
        $this->xPos = $m->getPosition()->getX();
        $this->yPos = $m->getPosition()->getY();
        $this->xVec = $m->getVector()->getX();
        $this->yVec = $m->getVector()->getY();
        return $this;
    }

    /**
     * returns a Motion object of the current move
     * @return Motion
     */
    public function getMotion()
    {
        return new Motion($this->getPosition(), $this->getVector());
    }

    /**
     * returns Position object part of move
     * @return Position
     */
    public function getPosition()
    {
        return new Position($this->getXPos(), $this->getYPos());
    }

    /**
     * returns Vector object part of move
     * @return Vector
     */
    public function getVector()
    {
        return new Vector($this->getXVec(), $this->getYVec());
    }

    public function getApiObject()
    {
        $m = array();
        $m["x"] = $this->getXPos();
        $m["y"] = $this->getYPos();
        $m["xv"] = $this->getXVec();
        $m["yv"] = $this->getYVec();
        if ($this->getCrash()) {
            $m["c"] = true;
        }
        $m["t"] = $this->getDate();
        if ($this->getMovemessage() != "") {
            $m["msg"] = $this->getMovemessage();
        }
        return $m;
    }


    public function isCrash()
    {
        return $this->getCrash();
    }

}
