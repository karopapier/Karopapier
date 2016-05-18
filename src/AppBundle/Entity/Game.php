<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="karo_games", indexes={@ORM\Index(name="U_ID", columns={"U_ID"})})
 * @ORM\Entity
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userId", referencedColumnName="id")
     * })
     */
    private $userId;


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
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="M_ID", type="integer", nullable=false)
     */
    private $mapId;

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

    public function getName()
    {
        return $this->name;
    }
    
    public function getDranUser()
    {
        return $this->userId;
    }

}
