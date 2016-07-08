<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Visits
 *
 * @ORM\Table(name="karo_visits")
 * @ORM\Entity
 */
class Visits
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="visitdate", type="date")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $visitdate;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="U_ID", referencedColumnName="U_ID")
     * @ORM\Id
     */
    private $user;
}

