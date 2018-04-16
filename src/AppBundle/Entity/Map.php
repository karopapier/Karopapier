<?php

namespace AppBundle\Entity;

use AppBundle\DTO\MapData;
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
    private $mapcode;

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

    private function __construct($id = 0)
    {
        if ($id > 0) {
            $this->id = $id;
        }
    }

    public static function createFromData(MapData $data)
    {
        $map = new Map($data->getId());
        $map->active = $data->active;
        $map->starties = $data->players;
        $map->author = $data->author;
        $map->name = $data->name;
        $map->mapcode = $data->mapcode;
        $map->cpsList = json_encode($data->cps);
        $map->nbCps = count($data->cps);

        return $map;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return str_replace("\r", "", $this->code);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
            "cps" => $this->getCpArray(),
            "active" => (bool)$this->active,
        );

        return $m;
    }
}
