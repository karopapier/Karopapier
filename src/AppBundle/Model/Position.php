<?php

namespace AppBundle\Model;

class Position
{
    private $x;
    private $y;

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function setX($newx)
    {
        $this->x = $newx;
    }

    public function setY($newy)
    {
        $this->y = $newy;
    }

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function __toString()
    {
        return '['.$this->x.'|'.$this->y.']';
    }

    public function getXY()
    {
        return array($this->x, $this->y);
    }

    public function move(Vector $v)
    {
        $this->setX($this->getX() + $v->getX());
        $this->setY($this->getY() + $v->getY());
    }

    /**
     * calculates a vector that leads from this pos to given pos
     * @param Position $p
     * @return Vector
     */
    public function getVectorTo(Position $p)
    {
        $vx = $p->getX() - $this->getX();
        $vy = $p->getY() - $this->getY();

        return new Vector($vx, $vy);
    }

    public function getPassedPositionsTo(Position $p)
    {
        $v = $this->getVectorTo($p);
        $vecs = $v->getPassedVectors();
        $positions = array();
        foreach ($vecs as $v) {
            $pos = clone $this;
            $pos->move($v);
            $positions[$pos->__toString()] = $pos;
        }

        return $positions;
    }
}