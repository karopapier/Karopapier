<?php

namespace AppBundle\Model;

class PositionCollection implements \Countable
{
    private $positions = array();

    public function offsetSet($offset, Position $value)
    {
        if ($offset == "") {
            $this->positions[] = $value;
        } else {
            $this->positions[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->positions[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->positions[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->positions[$offset]) ? $this->positions[$offset] : null;
    }

    public function rewind()
    {
        reset($this->positions);
    }

    public function current()
    {
        $pos = current($this->positions);

        return $pos;
    }

    public function key()
    {
        $pos = key($this->positions);

        return $pos;
    }

    public function next()
    {
        $pos = next($this->positions);

        return $pos;
    }

    public function valid()
    {
        $key = key($this->positions);
        $pos = ($key !== null && $key !== false);

        return $pos;
    }

    public function add(Position $pos)
    {
        $this->positions[$pos->__toString()] = $pos;
    }

    public function addXY($x, $y)
    {
        $pos = new Position($x, $y);
        $this->add($pos);
    }

    public function del(Position $pos)
    {
        unset($this->positions[$pos->__toString()]);
    }

    public function delXY($x, $y)
    {
        $pos = new Position($x, $y);
        $this->del($pos);
    }

    public function count()
    {
        return count($this->positions);
    }

    public function sort()
    {
        ksort($this->positions);
    }

    public function merge(PositionCollection $pc)
    {
        $this->positions = array_merge($this->positions, $pc->getArray());
    }

    public function getArray()
    {
        return $this->positions;
    }

    public function getApiObject()
    {
        $positions = array();
        foreach ($this->positions as $pos) {
            $positions[] = array(
                'x' => $pos->getX(),
                'y' => $pos->getY(),
            );
        }

        return $positions;
    }
}

