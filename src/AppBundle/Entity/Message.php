<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * KaroMessage
 *
 * @ORM\Table(name="karo_message", indexes={@ORM\Index(name="from_id", columns={"user_id"}), @ORM\Index(name="to_id", columns={"contact_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
{
    public function __construct(User $user, User $contact, $text, $rxtx)
    {
        $this->userId = $user->getId();
        $this->userName = $user->getUsername();
        $this->contactId = $contact->getId();
        $this->contactName = $contact->getUsername();
        $this->text = $text;
        $this->rxtx = $rxtx;
        $now = new \DateTime();
        $this->createdAt = $now;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=50, nullable=false)
     */
    private $userName;

    /**
     * @var integer
     *
     * @ORM\Column(name="contact_id", type="integer", nullable=false)
     */
    private $contactId;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_name", type="string", length=50, nullable=false)
     */
    private $contactName;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="rxtx", type="string", length=2, nullable=false)
     */
    private $rxtx = 'tx';

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
    private $isDeleted = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    public function isTx()
    {
        return ($this->rxtx === "tx");
    }

    public function toArray()
    {
        return [
            "id" => $this->id,
            "user_id" => $this->userId,
            "user_name" => $this->userName,
            "contact_id" => $this->contactId,
            "contact_name" => $this->contactName,
            "ts" => $this->createdAt->getTimestamp(),
            "r" => (int)(!is_null($this->readAt)),
            "text" => $this->text,
            "rxtx" => $this->rxtx,
        ];
    }


}

