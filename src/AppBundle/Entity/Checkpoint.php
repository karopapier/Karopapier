<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Checkpoints
 *
 * @ORM\Table(name="karo_checkpoints")
 * @ORM\Entity
 */
class Checkpoint
{
    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="checkpoints")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="G_ID", referencedColumnName="G_ID"),
     *   @ORM\JoinColumn(name="U_ID", referencedColumnName="U_ID")
     * })
     */
    private $player;

    /**
     * @var integer
     *
     * @ORM\Column(name="Checkpoint", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $checkpoint;

    /**
     * @return int
     */
    public function getCheckpoint()
    {
        return $this->checkpoint;
    }
}

