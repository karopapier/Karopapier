<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="karo_message", indexes={@ORM\Index(name="from_id", columns={"from_id"}), @ORM\Index(name="to_id", columns={"to_id"})})
 * @ORM\Entity
 */
class Message
{
    /**
     * @var integer
     *
     * @ORM\Column(name="from_id", type="integer", nullable=false)
     */
    private $fromId;

    /**
     * @var string
     *
     * @ORM\Column(name="from_name", type="string", length=50, nullable=false)
     */
    private $fromName;

    /**
     * @var integer
     *
     * @ORM\Column(name="to_id", type="integer", nullable=false)
     */
    private $toId;

    /**
     * @var string
     *
     * @ORM\Column(name="to_name", type="string", length=50, nullable=false)
     */
    private $toName;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="read_at", type="datetime", nullable=true)
     */
    private $readAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=false)
     */
    private $isDeleted;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}
