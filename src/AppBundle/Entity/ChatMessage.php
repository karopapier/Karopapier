<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * KaroChat
 *
 * @ORM\Table(name="karo_chat", uniqueConstraints={@ORM\UniqueConstraint(name="lineId", columns={"lineId"})})
 * @ORM\Entity
 */
class ChatMessage
{
    /**
     * @var integer
     * @ORM\Column(name="lineId", type="integer", nullable=false)
     */
    private $lineid;

    /**
     * Actual REAL timestamp of the message being sent, most probably recalculated for messages before 2017
     * @var \DateTime
     *
     * @ORM\Column(name="ts", type="datetime", nullable=true)
     */
    private $ts;


    /**
     * time string in hh:mm format that was originally written to the logfile (without date)
     * @var string
     * @ORM\Column(name="time", type="string", length=10, nullable=false)
     */
    private $time;

    /**
     * name of the 'user' that created the entry, might be fake or deleted user for very old manipulated entries
     * @var string
     * @ORM\Column(name="login", type="string", length=50, nullable=false)
     */
    private $login;

    /**
     * Optional UserId of the user
     * @var integer
     * @ORM\Column(name="U_ID", type="integer", nullable=true)
     */
    private $uId;

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=65535, nullable=false)
     */
    private $text;

    /**
     * For the sake of recalculating the original date, a ts that indicates the message must have been sent after this
     * @var \DateTime
     *
     * @ORM\Column(name="after", type="datetime", nullable=true)
     */
    private $after;

    /**
     * For the sake of recalculating the original date, a ts that indicates the message must have been sent before this
     * @var \DateTime
     *
     * @ORM\Column(name="before", type="datetime", nullable=true)
     */
    private $before;

    /**
     * Unique ID, not related to time, line or md5
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Create a new message and require a User to be given
     * ChatMessage constructor.
     * @param User $user
     * @param $text
     */
    public function __construct(User $user, $text)
    {
        $this->login = $user->getUsername();
        $this->uId = $user->getId();
        $this->text = $text;
        $this->raw = $text;
        //$text = KaroLayout::smilify($msg);
        $t = time();
        $this->ts = $t;
        $this->time = date("G:i", $t);
        $this->after = $t;
        $this->before = $t;
    }

    /**
     * @return \DateTime
     */
    public function getTs()
    {
        return $this->ts;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }
}
