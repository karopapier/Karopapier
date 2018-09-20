<?php

namespace AppBundle\Entity;

use AppBundle\DTO\MapData;
use AppBundle\Model\Motion;
use AppBundle\Model\Position;
use AppBundle\Model\PositionCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Map
 *
 * @ORM\Table(name="karo_maps")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MapRepository")
 */
class Map
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

    public function updateFromData(MapData $mapData)
    {
        $this->active = $mapData->active;
        $this->starties = $mapData->players;
        $this->author = $mapData->author;
        $this->name = $mapData->name;
        $this->mapcode = $mapData->mapcode;
        $this->cpsList = json_encode($mapData->cps);
        $this->nbCps = count($mapData->cps);
    }

    public static function createFromData(MapData $mapData)
    {
        $map = new Map($mapData->getId());
        $map->updateFromData($mapData);

        return $map;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return str_replace("\r", "", $this->mapcode);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->active;
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
            // "mapcode" => $this->getCode(),
            "cps" => $this->getCpArray(),
            "active" => (bool)$this->active,
            "night" => $this->night,
        );

        return $m;
    }

    public function getPlayers()
    {
        return $this->starties;
    }

    public function isNight()
    {
        return (bool)$this->night;
    }

    public function updateRating($rating)
    {
        $this->rating = $rating;
    }

    public function getSizeForWidthAndHeight($width, $height)
    {
        if ($width) {
            return ceil($width / $this->getNbCols());
        }

        if ($height) {
            return ceil($height / $this->getNbRows());
        }

        return 12;
    }

    /**
     * @return bool
     */
    public function getHasCheckpoints()
    {
        return (count($this->getCpArray()) > 0);
    }

    public function getNbCols()
    {
        $matrix = $this->getMatrix();

        return strlen($matrix[0]);
    }

    public function getNbRows()
    {
        $matrix = $this->getMatrix();

        return count($matrix);
    }

    public function getMatrix()
    {
        if (isset($this->matrix)) {
            return $this->matrix;
        }

        $matrix = array();
        $code = $this->getCode();
        $lines = explode("\n", $code);
        foreach ($lines as $line) {
            $matrix[] = $line;
        }
        $this->matrix = $matrix;

        return $matrix;
    }

    public function getFieldAtPosition(Position $position)
    {
        $x = $position->getX();
        $y = $position->getY();

        $matrix = $this->getMatrix();
        $row = $matrix[$y];

        return $row[$x];
    }

    public function getPassedFields(Motion $motion)
    {
        $srcPos = $motion->getSourcePosition();

        return $this->getPassedFieldTypes($srcPos, $motion->getPosition());
    }

    /**
     * returns a sorted array of all field types that are passed on a vector from Pos1 to Pos2
     * order must be kept to be able to check what is passed first
     * @param Position $pos1
     * @param Position $pos2
     * @return array
     */
    public function getPassedFieldTypes(Position $pos1, Position $pos2)
    {
        $v = $pos1->getVectorTo($pos2);
        $passedPositions = $pos1->getPassedPositionsTo($pos2);
        $fields = array();
        foreach ($passedPositions as $pos) {
            $fields[] = $this->getFieldAtPosition($pos);
        }

        return $fields;
    }

    /**
     * returns all fields of given codes as Positions
     * @return PositionCollection
     */
    public function getCodePositions($codes)
    {
        $positions = new PositionCollection();
        foreach ($this->getMatrix() as $y => $xs) {
            $startXs = array_keys(
                array_filter(
                    str_split($xs),
                    function ($x) use (&$codes) {
                        return in_array($x, $codes);
                    }
                )
            );
            foreach ($startXs as $x) {
                $positions->addXY($x, $y);
            }
        }
        $positions->sort();

        return $positions;
    }

    /**
     * returns all Start fields of the map as Positions
     * @return array of Postition
     */
    public function getStartPositions()
    {
        return $this->getCodePositions(array("S"));
    }
}
