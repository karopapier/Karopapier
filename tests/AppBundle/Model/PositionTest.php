<?php

use AppBundle\Model\Position;
use AppBundle\Model\Vector;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    private function is($a, $b, $c)
    {
        $this->assertEquals($a, $b, $c);
    }

    private function ok($a, $b)
    {
        $this->assertTrue($a, $b);
    }

    public function testPost()
    {
        $t = $this;


//getVectorTo
        $pos1 = new Position(3, 3);
        $pos2 = new Position(2, 2);
        $vec = $pos1->getVectorTo($pos2);
        $target = new Vector(-1, -1);
        $t->ok(($vec == $target), '->getVectorTo returns correct vector');

        $pos1 = new Position(2, 1);
        $pos2 = new Position(2, 7);
        $vec = $pos1->getVectorTo($pos2);
        $target = new Vector(0, 6);
        $t->ok(($vec == $target), '->getVectorTo returns correct vector');


//getPassedPositionsTo
        $pos1 = new Position(2, 1);
        $pos2 = new Position(2, 4);
        $positions = $pos1->getPassedPositionsTo($pos2);
        $target = array('[2|1]', '[2|2]', '[2|3]', '[2|4]');
        $t->is(array_keys($positions), $target, '->getPassedPositionsTo calculates correct positions in order');

//getPassedPositionsTo
        $pos1 = new Position(7, 8);
        $pos2 = new Position(1, 1);
        $positions = $pos1->getPassedPositionsTo($pos2);
        $target = array(
            '[7|8]',
            '[7|7]',
            '[6|7]',
            '[6|6]',
            '[5|6]',
            '[5|5]',
            '[4|5]',
            '[4|4]',
            '[3|4]',
            '[3|3]',
            '[2|3]',
            '[2|2]',
            '[1|2]',
            '[1|1]',
        );
        $t->is(array_keys($positions), $target, '->getPassedPositionsTo calculates correct positions in order');
    }
}

