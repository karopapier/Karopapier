<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mapvote
 *
 * @ORM\Table(name="karo_mapvotes")
 * @ORM\Entity
 */
class Mapvote
{
    /**
     * @var integer
     *
     * @ORM\Column(name="Vote", type="integer", nullable=false)
     */
    private $vote;

    /**
     * @var string
     *
     * @ORM\Column(name="Comment", type="string", length=255, nullable=false)
     */
    private $comment;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="U_ID", referencedColumnName="U_ID")
     */
    private $user;

    /**
     * @var Map
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Map", inversedBy="votes")
     * @ORM\JoinColumn(name="M_ID", referencedColumnName="M_ID")
     */
    private $map;

}
