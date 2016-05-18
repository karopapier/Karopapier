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
     * @var integer
     *
     * @ORM\Column(name="G_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $gameId;

    /**
     * @var integer
     *
     * @ORM\Column(name="U_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;


}
