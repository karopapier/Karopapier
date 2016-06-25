<?php

namespace AppBundle\Entity;

use AppBundle\Model\BaseMap;
use Doctrine\ORM\Mapping as ORM;

/**
 * Map
 *
 * @ORM\Table(name="karo_maps")
 * @ORM\Entity
 */
class Map extends BaseMap
{
    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="Code", type="text", length=65535, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="Author", type="string", length=255, nullable=false)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="Comment", type="text", length=65535, nullable=false)
     */
    private $comment;

    /**
     * @var integer
     *
     * @ORM\Column(name="Night", type="integer", nullable=false)
     */
    private $night;

    /**
     * @var integer
     *
     * @ORM\Column(name="Record", type="integer", nullable=false)
     */
    private $record;

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
     * @var integer
     *
     * @ORM\Column(name="cps_rec", type="integer", nullable=false)
     */
    private $cpsRec;

    /**
     * @var integer
     *
     * @ORM\Column(name="Active", type="integer", nullable=false)
     */
    private $active;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", precision=10, scale=0, nullable=false)
     */
    private $rating;

    /**
     * @var integer
     *
     * @ORM\Column(name="M_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }

    public function setMapcode($mapcode)
    {
        $this->code = $mapcode;
    }

    public function getCode()
    {
        return str_replace("\r", "", $this->code);
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function getCpArray()
    {
        return json_decode($this->cpsList);
    }

    public function toArray()
    {
        $m = array(
                "id" => $this->id,
                "name" => $this->name,
                "author" => $this->author,
                "cols" => $this->getNbCols(),
                "rows" => $this->getNbRows(),
                "rating" => $this->rating,
                "players" => $this->starties,
                "mapcode" => $this->getCode(),
                "cps" => $this->getCpArray()
        );
        return $m;
    }
}
