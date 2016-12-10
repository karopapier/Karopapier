<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\DateTime;

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
     * @ORM\Column(name="Vorname", type="string", length=255, nullable=true)
     */
    private $vorname = "";

    /**
     * @var string
     *
     * @ORM\Column(name="Nachname", type="string", length=255, nullable=true)
     */
    private $nachname = "";

    /**
     * @var string
     *
     * @ORM\Column(name="Email", type="string", length=50, nullable=false)
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
     * @ORM\Column(name="ICQ", type="string", length=255, nullable=true)
     */
    private $icq;

    /**
     * @var string
     *
     * @ORM\Column(name="AIM", type="string", length=255, nullable=true)
     */
    private $aim;

    /**
     * @var string
     *
     * @ORM\Column(name="MSN", type="string", length=255, nullable=true)
     */
    private $msn;

    /**
     * @var string
     *
     * @ORM\Column(name="Jabber", type="string", length=255, nullable=true)
     */
    private $jabber;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter", type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @var string
     *
     * @ORM\Column(name="xing", type="string", length=255, nullable=true)
     */
    private $xing;

    /**
     * @var string
     *
     * @ORM\Column(name="linkedin", type="string", length=255, nullable=true)
     */
    private $linkedin;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @var string
     *
     * @ORM\Column(name="myspace", type="string", length=255, nullable=true)
     */
    private $myspace;

    /**
     * @var string
     *
     * @ORM\Column(name="Picture", type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="short_info", type="string", length=255, nullable=true)
     */
    private $shortInfo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastvisit", type="datetime", nullable=true)
     */
    private $lastvisit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reallastvisit", type="datetime", nullable=true)
     */
    private $reallastvisit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="currentvisit", type="datetime", nullable=true)
     */
    private $currentvisit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="chatvisit", type="datetime", nullable=true)
     */
    private $chatvisit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastmailsent", type="datetime", nullable=true)
     */
    private $lastmailsent;

    /**
     * @var string
     *
     * @ORM\Column(name="Browser", type="string", length=255, nullable=true)
     */
    private $browser;

    /**
     * @var string
     *
     * @ORM\Column(name="Color", type="string", length=6, nullable=false)
     */
    private $color = 'ff0000';

    /**
     * @var integer
     *
     * @ORM\Column(name="Size", type="integer", nullable=false)
     */
    private $size = 12;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Border", type="integer", nullable=false)
     */
    private $border = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="View", type="integer", nullable=false)
     */
    private $view = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="draw_limit", type="integer", nullable=false)
     */
    private $drawLimit = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="GamesPerPage", type="integer", nullable=false)
     */
    private $gamesperpage = 25;

    /**
     * @var string
     *
     * @ORM\Column(name="games_order", type="string", length=255, nullable=false)
     */
    private $gamesOrder = "name";

    /**
     * @var string
     *
     * @ORM\Column(name="theme", type="string", length=50, nullable=false)
     */
    private $theme = "karo1";

    /**
     * @var boolean
     *
     * @ORM\Column(name="Active", type="boolean", nullable=false)
     */
    private $active = false;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="Invited", referencedColumnName="U_ID", nullable=true)
     */
    private $invited = null;

    /**
     * @var integer
     *
     * @ORM\Column(name="invited2", type="integer", nullable=true)
     */
    private $invited2 = null;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Sendmail", type="boolean", nullable=false)
     */
    private $sendmail = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Tag", type="boolean", nullable=false)
     */
    private $tag = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Nacht", type="boolean", nullable=false)
     */
    private $nacht = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="Maxgames", type="integer", nullable=false)
     */
    private $maxgames = 0;

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
    private $session = "0";

    /**
     * @var boolean
     *
     * @ORM\Column(name="Warned", type="boolean", nullable=false)
     */
    private $warned = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="automoves", type="integer", nullable=false)
     */
    private $automoves = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="maillock", type="datetime", nullable=true)
     */
    private $maillock;

    /**
     * @var integer
     *
     * @ORM\Column(name="isbot", type="boolean", nullable=false)
     */
    private $isbot = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var integer
     *
     * @ORM\Column(name="gelesen", type="integer", nullable=false)
     */
    private $gelesen = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="faulpelz", type="integer", nullable=false)
     */
    private $faulpelz = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="move_autoforward", type="smallint", nullable=false)
     */
    private $moveAutoforward = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="economode", type="boolean", nullable=false)
     */
    private $economode = true;

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
    private $activejs = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activeCanvas", type="boolean", nullable=false)
     */
    private $activecanvas = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status_code", type="boolean", nullable=false)
     */
    private $statusCode = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="status_text", type="string", length=255, nullable=false)
     */
    private $statusText = "";

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
    private $nbGames = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_wollust", type="integer", nullable=false)
     */
    private $maxWollust = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="use_bart", type="boolean", nullable=true)
     */
    private $useBart = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="use_sound", type="integer", nullable=false)
     */
    private $useSound = true;

    /**
     * @var string
     *
     * @ORM\Column(name="notification_sound", type="string", length=50, nullable=false)
     */
    private $notificationSound = "brumm";

    public function __construct()
    {
        $this->signupdate = new \DateTime('now');
    }

    public function init($data)
    {
        foreach ($data as $prop => $value) {
            $this->$prop = $value;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->getLogin();
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getRoles()
    {
        if ($this->id == 1) return array("ROLE_USER", "ROLE_ADMIN");
        if ($this->login == "Didi") return array("ROLE_USER", "ROLE_ADMIN");
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

    public function getNbGames()
    {
        return $this->nbGames;
    }

    /**
     * spielegeil?
     */
    public function isDesperate()
    {
        return ($this->statusCode == 10);
    }

    /**
     * Today birthday?
     */
    public function isBirthdayToday()
    {
        if ($b = $this->birthday->format("md")) {
            if ($b == date('md', time())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Today karoday?
     */
    public function isKarodayToday()
    {
        if ($b = $this->signupdate->format("md")) {
            if ($b == date('md', time())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getSoundfile()
    {
        if ($this->notificationSound) {
            return "/mp3/" . $this->notificationSound . ".mp3";
        }
        return false;
    }

    public function toArray()
    {
        return array(
                "id" => $this->id,
                "login" => $this->login,
                "color" => $this->color,
                "lastVisit" => $this->getNbDaysAbsent(),
                "signup" => $this->getNbDaysSignedUp(),
                "dran" => $this->nbDran,
                "activeGames" => $this->nbGames,
                "acceptsDayGames" => $this->tag,
                "acceptsNightGames" => $this->nacht,
                "maxGames" => $this->maxgames,
                "sound" => $this->useSound,
                "soundfile" => $this->getSoundfile(),
                "size" => $this->size,
                "border" => $this->border,
                "desperate" => $this->isDesperate(),
                "birthdayToday" => $this->isBirthdayToday(),
                "karodayToday" => $this->isKarodayToday(),
                "theme" => $this->theme,
                "bot" => $this->isbot,
        );

    }

    public function touch()
    {
        $this->currentvisit = new \DateTime('now');
        $this->active = true;
    }

    public function visitUpdate($userAgent)
    {
        $this->warned = false;
        $this->lastvisit = $this->currentvisit;
        $this->reallastvisit = $this->currentvisit;
        $this->browser = $userAgent;
    }

    public function __toString()
    {
        return $this->login;
    }

    public function asLegacySessionArray()
    {
        return array(
                "login" => $this->login,
                "u_id" => $this->id,
                "vorname" => $this->vorname,
                "nachname" => $this->nachname,
                "size" => $this->size,
                "border" => $this->border,
                "view" => $this->view,
                "GamesPerPage" => $this->gamesperpage,
                "games_order" => $this->gamesOrder,
                "faulpelz" => $this->faulpelz,
                "move_autoforward" => $this->moveAutoforward,
                "economode" => $this->economode,
                "draw_limit" => $this->drawLimit
        );
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSignupdate()
    {
        return $this->signupdate;
    }

    public function getRealLastvisit()
    {
        return $this->reallastvisit;
    }

    public function getGravatar()
    {
        $grav = 'http://www.gravatar.com/avatar/' . md5(strtolower($this->email));
        return $grav;
    }

    public function getBirthday()
    {
        return $this->birthday;

    }

    public function getShortInfo()
    {
        return $this->shortInfo;
    }

    public function getInvitor()
    {
        return $this->invited;
    }

    public function isBot()
    {
        return (bool)$this->isbot;
    }

    /**
     * @return string
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

}
