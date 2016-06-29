<?php

namespace AppBundle\Entity;

use AppBundle\Model\BaseMap;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Mapcode as Mapcode;

/**
 * UserMap
 *
 * @Assert\GroupSequence({"UserMap", "Analysis"})
 * @ORM\Table(name="karo_user_map")
 * @ORM\Entity
 */
class UserMap extends BaseMap
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="U_ID")
     */
    private $author;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @Assert\Regex(pattern="/S/",message="Braucht ein Startfeld S")
     * @Assert\Regex(pattern="/F/",message="Braucht ein Zielfeld F")
     * @Mapcode\LinesEqualLength()
     * @Mapcode\HasBorder(groups={"Analysis"})    //only do this when all simple checks pass
     * @Mapcode\Finishable(groups={"Analysis"})    //only do this when all simple checks pass
     *
     * @ORM\Column(name="mapcode", type="text", length=65535, nullable=false)
     */
    private $mapcode;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=false)
     */
    private $comment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="used", type="boolean", nullable=false, options={"default":false})
     */
    private $used;

    /**
     * @var boolean
     *
     * @ORM\Column(name="archived", type="boolean", nullable=false, options={"default":false})
     */
    private $archived;

    /**
     * @var integer
     *
     * @ORM\Column(name="Starties", type="integer", nullable=false)
     */
    private $starties;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_cps", type="integer", nullable=false)
     */
    private $nbCps;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_list", type="string", length=50, nullable=false)
     */
    private $cpsList;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", precision=10, scale=0, nullable=false)
     */
    private $rating;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    public function __construct()
    {
        $this->rating = 0;
        $this->used = false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMapcode()
    {
        return $this->mapcode;
    }

    /**
     * @param string $mapcode
     */
    public function setMapcode($mapcode)
    {
        $this->mapcode = $mapcode;
        //unique list of chars
        $chars = count_chars($mapcode, 3);
        //only ints
        $ints = preg_replace('/[^0-9]+/', '', $chars);
        //split and sort them
        $cps = array();
        if ($ints != "") {
            $cps = str_split($ints);
        }
        sort($cps);
        $this->cpsList = json_encode($cps);
        $this->nbCps = count($cps);
        $this->starties = substr_count($mapcode, "S");
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return boolean
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param boolean $archived
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;
    }

    public function getCode()
    {
        return str_replace("\r", "", $this->mapcode);
    }

    public function getCpArray()
    {
        if (!$this->cpsList) return array();
        return json_decode($this->cpsList);
    }

    public function getAuthorName()
    {
        return $this->author->getUsername();
    }

    public function toArray()
    {
        $m = array(
                "id" => "u" . $this->id,
                "name" => $this->name,
                "author" => $this->getAuthorName(),
                "cols" => $this->getNbCols(),
                "rows" => $this->getNbRows(),
                "rating" => $this->rating,
                "players" => $this->starties,
                "mapcode" => $this->getCode(),
                "cps" => $this->getCpArray()
        );
        return $m;
    }

    public function setAuthor(User $user)
    {
        $this->author = $user;
    }
}
