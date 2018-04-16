<?php

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserMap
 *
 * @Assert\GroupSequence({"UserMap", "Analysis"})
 */
class Mapcode
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @Assert\Regex(pattern="/S/",message="Braucht ein Startfeld S")
     * @Assert\Regex(pattern="/F/",message="Braucht ein Zielfeld F")
     * @MapcodeConstraint\LinesEqualLength()
     * @MapcodeConstraint\HasBorder(groups={"Analysis"})    //only do this when all simple checks pass
     * @MapcodeConstraint\Finishable(groups={"Analysis"})    //only do this when all simple checks pass
     *
     * @var string $mapcode
     */
    private $mapcode;

    /** @var integer */
    private $players;

    /** @var array */
    private $cps;

    /** @var int */
    private $cols = 0;

    /** @var int */
    private $rows = 0;

    public function __construct($mapcode)
    {
        $this->mapcode = str_replace("\r", "", trim($mapcode));
        $this->parse();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->mapcode;
    }

    public function parse()
    {
        $mapcode = $this->mapcode;
        // unique list of chars
        $lines = explode("\n", $mapcode);
        $chars = count_chars($mapcode, 3);
        // only keep ints/cps
        $ints = preg_replace('/[^0-9]+/', '', $chars);
        // split and sort them
        $cps = array();
        if ($ints != "") {
            $cps = str_split($ints);
            //cast strings to int and sort
            $cps = array_map('intval', $cps);
            sort($cps);
        }

        $this->cps = $cps;
        $this->players = substr_count($mapcode, "S");
        $this->rows = count($lines);
        $this->cols = strlen($lines[0]);
    }

    public function toArray()
    {
        $m = array(
            "cols" => $this->cols,
            "rows" => $this->rows,
            "players" => $this->players,
            "mapcode" => $this->mapcode,
            "cps" => $this->cps,
        );

        return $m;
    }
}
