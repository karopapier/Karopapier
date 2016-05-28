<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="karo_user", indexes={@ORM\Index(name="i_login", columns={"Login"})})
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="U_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Login", type="string", length=20, nullable=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="Old_Passwd", type="string", length=20, nullable=true)
     */
    private $oldPasswd;

    /**
     * @var string
     *
     * @ORM\Column(name="Passwd", type="string", length=255, nullable=false)
     */
    private $passwd;

    /**
     * @var string
     *
     * @ORM\Column(name="Vorname", type="string", length=255, nullable=false)
     */
    private $vorname;

    /**
     * @var string
     *
     * @ORM\Column(name="Nachname", type="string", length=255, nullable=false)
     */
    private $nachname;

    /**
     * @var string
     *
     * @ORM\Column(name="Email", type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="Homepage", type="string", length=60, nullable=true)
     */
    private $homepage;

    /**
     * @var string
     *
     * @ORM\Column(name="ICQ", type="string", length=255, nullable=false)
     */
    private $icq;

    /**
     * @var string
     *
     * @ORM\Column(name="AIM", type="string", length=255, nullable=false)
     */
    private $aim;

    /**
     * @var string
     *
     * @ORM\Column(name="MSN", type="string", length=255, nullable=false)
     */
    private $msn;

    /**
     * @var string
     *
     * @ORM\Column(name="Jabber", type="string", length=255, nullable=false)
     */
    private $jabber;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter", type="string", length=255, nullable=false)
     */
    private $twitter;

    /**
     * @var string
     *
     * @ORM\Column(name="xing", type="string", length=255, nullable=false)
     */
    private $xing;

    /**
     * @var string
     *
     * @ORM\Column(name="linkedin", type="string", length=255, nullable=false)
     */
    private $linkedin;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=255, nullable=false)
     */
    private $facebook;

    /**
     * @var string
     *
     * @ORM\Column(name="myspace", type="string", length=255, nullable=false)
     */
    private $myspace;

    /**
     * @var string
     *
     * @ORM\Column(name="Picture", type="string", length=255, nullable=false)
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="short_info", type="string", length=255, nullable=false)
     */
    private $shortInfo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastvisit", type="datetime", nullable=false)
     */
    private $lastvisit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reallastvisit", type="datetime", nullable=false)
     */
    private $reallastvisit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="currentvisit", type="datetime", nullable=false)
     */
    private $currentvisit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="chatvisit", type="datetime", nullable=false)
     */
    private $chatvisit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastmailsent", type="datetime", nullable=false)
     */
    private $lastmailsent;

    /**
     * @var string
     *
     * @ORM\Column(name="Browser", type="string", length=255, nullable=false)
     */
    private $browser;

    /**
     * @var string
     *
     * @ORM\Column(name="Color", type="string", length=6, nullable=false)
     */
    private $color;

    /**
     * @var integer
     *
     * @ORM\Column(name="Size", type="integer", nullable=false)
     */
    private $size;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Border", type="boolean", nullable=false)
     */
    private $border;

    /**
     * @var integer
     *
     * @ORM\Column(name="View", type="integer", nullable=false)
     */
    private $view;

    /**
     * @var integer
     *
     * @ORM\Column(name="draw_limit", type="integer", nullable=false)
     */
    private $drawLimit;

    /**
     * @var integer
     *
     * @ORM\Column(name="GamesPerPage", type="integer", nullable=false)
     */
    private $gamesperpage;

    /**
     * @var string
     *
     * @ORM\Column(name="games_order", type="string", length=255, nullable=false)
     */
    private $gamesOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="theme", type="string", length=50, nullable=false)
     */
    private $theme;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="Invited", type="integer", nullable=false)
     */
    private $invited;

    /**
     * @var integer
     *
     * @ORM\Column(name="invited2", type="integer", nullable=false)
     */
    private $invited2;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Sendmail", type="boolean", nullable=false)
     */
    private $sendmail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Tag", type="boolean", nullable=false)
     */
    private $tag;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Nacht", type="boolean", nullable=false)
     */
    private $nacht;

    /**
     * @var integer
     *
     * @ORM\Column(name="Maxgames", type="integer", nullable=false)
     */
    private $maxgames;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Signupdate", type="datetime", nullable=false)
     */
    private $signupdate;

    /**
     * @var string
     *
     * @ORM\Column(name="Session", type="string", length=255, nullable=false)
     */
    private $session;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Warned", type="boolean", nullable=false)
     */
    private $warned;

    /**
     * @var integer
     *
     * @ORM\Column(name="automoves", type="integer", nullable=false)
     */
    private $automoves;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="maillock", type="datetime", nullable=false)
     */
    private $maillock;

    /**
     * @var integer
     *
     * @ORM\Column(name="isbot", type="integer", nullable=false)
     */
    private $isbot;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Birthday", type="date", nullable=false)
     */
    private $birthday;

    /**
     * @var integer
     *
     * @ORM\Column(name="gelesen", type="integer", nullable=false)
     */
    private $gelesen;

    /**
     * @var integer
     *
     * @ORM\Column(name="faulpelz", type="integer", nullable=false)
     */
    private $faulpelz;

    /**
     * @var integer
     *
     * @ORM\Column(name="move_autoforward", type="smallint", nullable=false)
     */
    private $moveAutoforward;

    /**
     * @var boolean
     *
     * @ORM\Column(name="economode", type="boolean", nullable=false)
     */
    private $economode;

    /**
     * @var string
     *
     * @ORM\Column(name="geoLat", type="string", length=30, nullable=true)
     */
    private $geolat;

    /**
     * @var string
     *
     * @ORM\Column(name="geoLng", type="string", length=30, nullable=true)
     */
    private $geolng;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activeJS", type="boolean", nullable=false)
     */
    private $activejs;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activeCanvas", type="boolean", nullable=false)
     */
    private $activecanvas;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status_code", type="boolean", nullable=false)
     */
    private $statusCode;

    /**
     * @var string
     *
     * @ORM\Column(name="status_text", type="string", length=255, nullable=false)
     */
    private $statusText;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="status_date", type="date", nullable=true)
     */
    private $statusDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_dran", type="integer", nullable=true)
     */
    private $nbDran;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_games", type="integer", nullable=true)
     */
    private $nbGames;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_wollust", type="integer", nullable=false)
     */
    private $maxWollust;

    /**
     * @var boolean
     *
     * @ORM\Column(name="use_bart", type="boolean", nullable=true)
     */
    private $useBart;

    /**
     * @var boolean
     *
     * @ORM\Column(name="use_sound", type="boolean", nullable=false)
     */
    private $useSound;

    /**
     * @var string
     *
     * @ORM\Column(name="notification_sound", type="string", length=50, nullable=false)
     */
    private $notificationSound;

    public function getId()
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getRoles()
    {
        if ($this->id == 1) return array("ROLE_USER", "ROLE_ADMIN");
        return array("ROLE_USER");
    }

    public function getPassword()
    {
        return $this->passwd;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->login;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * number of days a user is signed up at karopapier
     * @return integer
     */
    public function getNbDaysSignedUp()
    {
        #how many days since sign up 
        $now = new \DateTime('now');
        return $now->diff($this->signupdate)->days;
    }

    /**
     * number of days a user is absent from karopapier
     * @return integer
     */
    public function getNbDaysAbsent()
    {
        #how many days passed since last visit
        $now = new \DateTime('now');
        return $now->diff($this->reallastvisit)->days;
    }

    /**
     * @return int
     */
    public function getNbDran()
    {
        return $this->nbDran;
    }
}
