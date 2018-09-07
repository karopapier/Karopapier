<?php

namespace AppBundle\Model;

/**
 *
 * @author pdietrich
 *
 * A Motion consists of a Position and a Vector. The Vector represents the current momentum, being the vector that "brought the Player here"
 * This means, the Position of a Motion represents the "target" or end point of the motion - NOT THE SOURCE (can be obtained with getSourcePosition() )
 *
 */
class Motion
{
    public $pos;
    public $vec;

    public static function createFromXYV($x, $y, $xv, $yv)
    {
        $pos = new Position($x, $y);
        $vec = new Vector($xv, $yv);

        return new Motion($pos, $vec);
    }

    public function __toString()
    {
        return $this->getPosition()->__toString().' '.$this->getVector()->__toString();
    }

    public function __toKeyString()
    {
        return $this->getPosition()->__toString();
    }

    public function __construct(Position $pos, Vector $vec)
    {
        $this->pos = $pos;
        $this->vec = $vec;
    }

    public function __clone()
    {
        $this->pos = clone $this->pos;
        $this->vec = clone $this->vec;
    }

    /**
     * @return Position
     */
    public function getPosition()
    {
        return $this->pos;
    }

    /**
     * @return Vector
     */
    public function getVector()
    {
        return $this->vec;
    }

    public function setPosition($pos)
    {
        $this->pos = $pos;
    }

    public function setVector($vec)
    {
        $this->vec = $vec;
    }

    /**
     * @return Position
     */
    public function getStopPosition()
    {
        $pos = clone $this->getPosition();
        $vec = clone $this->getVector();

        while ($vec->getLength() > 0) {
            #echo $pos;
            #echo " + ";
            #echo $vec;
            #echo "\n";
            $pos->move($vec);
            $vec->decelerate();
        }

        return $pos;
    }

    /**
     * @return Position
     */
    public function getSourcePosition()
    {
        $p = $this->getPosition();
        $x = $p->getX() - $this->getVector()->getX();
        $y = $p->getY() - $this->getVector()->getY();

        return new Position($x, $y);
    }

    public function getStopMovesCount()
    {
        return max(abs($this->getVector()->getX()), abs($this->getVector()->getY()));
    }

    /**
     * returns an array of the 9 theoretically possible motions, indexed by position
     * @return array
     */
    public function getNextMotionsPositionIndex()
    {
        $nm = Array();
        #walk the 9 possibilities to have them arranged like
        # 1 2 3
        # 4 5 6
        # 7 8 9
        for ($tY = -1; $tY <= 1; $tY++) {
            for ($tX = -1; $tX <= 1; $tX++) {
                $v = clone $this->getVector();
                $v->setX($v->getX() + $tX);
                $v->setY($v->getY() + $tY);
                $m = clone $this;
                $m->move($v);
                $nm[$m->__toKeyString()] = $m;
            }
        }

        return $nm;
    }

    /**
     * @return array Motions
     */
    public function getNextMotions()
    {
        $nm = Array();
        #walk the 9 possibilities to have them arranged like
        # 1 2 3
        # 4 5 6
        # 7 8 9
        $tcount = 1;
        for ($tY = -1; $tY <= 1; $tY++) {
            for ($tX = -1; $tX <= 1; $tX++) {
                $nm[$tcount] = clone $this;
                $nm[$tcount]->getVector()->setX($nm[$tcount]->getVector()->getX() + $tX);
                $nm[$tcount]->getVector()->setY($nm[$tcount]->getVector()->getY() + $tY);
                $tcount++;
            }
        }

        return $nm;
    }

    public function getNextMotionsSortedByLength()
    {
        $nm = $this->getNextMotions();
        $nml = array();
        while (list($key, $m) = each($nm)) {
            $nml[$key] = $m->getVector()->getLength();
        }
        asort($nml);

        $nmbl = Array();
        while (list($key, $l) = each($nml)) {
            $nmbl[$key] = $nm[$key];
        }

        return $nmbl;
    }

    /**
     * applies a vector on the current Motion, modifing the positon
     * @param Vector
     */
    public function move(Vector $v)
    {
        $this->getPosition()->move($v);
        $this->setVector($v);
    }

    public function getIlluminatedPositions($lightRange = 5)
    {
        $xpos = $this->getSourcePosition()->getX();
        $ypos = $this->getSourcePosition()->getY();
        $xvec = $this->getVector()->getX();
        $yvec = $this->getVector()->getY();

        $illuminated = new PositionCollection();
        if (($xvec == 0) && ($yvec == 0)) {
            for ($j = -2; $j <= 2; $j++) {
                for ($k = -2; $k <= 2; $k++) {
                    $illuX = $xpos + $j;
                    $illuY = $ypos + $k;
                    $illuminated->addXY($illuX, $illuY);
                }
            }
        } else {
            $factor = $lightRange / sqrt(($xvec * $xvec) + ($yvec * $yvec));
            //echo "L�ngenfactor: $factor<BR>";
            $xfactor = round($xvec * $factor);
            $yfactor = round($yvec * $factor);

            for ($i = 0; $i <= $lightRange; $i++) {
                $illuX = round($xpos + $xfactor * $i / $lightRange);
                $illuY = round($ypos + $yfactor * $i / $lightRange);
                $illuminated->addXY($illuX, $illuY);

                for ($j = -1; $j <= 1; $j++) {
                    for ($k = -1; $k <= 1; $k++) {
                        $illuX2 = $illuX + $j;
                        $illuY2 = $illuY + $k;
                        $illuminated->addXY($illuX2, $illuY2);
                    }
                }
            }
        }
        $illuminated->sort();

        return $illuminated;
    }

    public function getApiObject()
    {
        $m = new stdClass();
        $m->x = $this->getPosition()->getX();
        $m->y = $this->getPosition()->getY();
        $m->xv = $this->getVector()->getX();
        $m->yv = $this->getVector()->getY();

        return $m;
    }
}
