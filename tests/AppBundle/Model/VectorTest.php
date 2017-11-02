<?php

use AppBundle\Model\Vector;
use PHPUnit\Framework\TestCase;

class MotionTest extends TestCase
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

        $v = new Vector(-5, 7);
        $t->is($v->__toString(), '(-5|7)', '->toString returns (|)');

        $v = new Vector(2, 2);
        $order = array("(0|0)", "(1|1)", "(2|2)");
        $t->is(array_keys($v->getPassedVectors()), $order, '->getPassedVectors returns correct passed vectors');

        $v = new Vector(0, 3);
        $order = array("(0|0)", "(0|1)", "(0|2)", "(0|3)");
        $t->is(array_keys($v->getPassedVectors()), $order, '->getPassedVectors returns correct passed vectors');

        $v = new Vector(-2, 1);
        $order = array("(0|0)", "(-1|0)", "(-1|1)", "(-2|1)");
        $t->is(array_keys($v->getPassedVectors()), $order, '->getPassedVectors returns correct passed vectors');

        $v = new Vector(-3, -1);
        $order = array("(0|0)", "(-1|0)", "(-2|-1)", "(-3|-1)");
        $t->is(array_keys($v->getPassedVectors()), $order, '->getPassedVectors returns correct passed vectors');
    }
}