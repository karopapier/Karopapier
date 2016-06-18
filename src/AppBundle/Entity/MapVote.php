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
     * @var integer
     *
     * @ORM\Column(name="U_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uId;

    /**
     * @var integer
     *
     * @ORM\Column(name="M_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $mId;


}
