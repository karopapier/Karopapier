<?php

use AppBundle\Model\Position;
use AppBundle\Model\PositionCollection;
use Tests\AppBundle\LimeWrapperTestCase;

class PositionCollectionTest extends LimeWrapperTestCase
{
    public function testAdd()
    {
        $t = $this;
        $ps = new PositionCollection();
        $t->is(count($ps), 0, 'Interface Countable correctly implemented');

        $ps->addXY(7, 5);
        $t->is(count($ps), 1, 'Interface Countable correctly implemented');

        $ps->addXY(7, 5);
        $t->is(count($ps), 1, 'Same not added twice');

        $p = new Position(4, 6);
        $ps->add($p);
        $t->is(count($ps), 2, 'Position is added');
        $t->is(array_keys($ps->getArray()), array("[7|5]", "[4|6]"), '->addXY() adds Positions to the collection');

        $ps->sort();
        $t->is(array_keys($ps->getArray()), array("[4|6]", "[7|5]"), '->sort() changes the order correctly');
        $t->is(count($ps), 2, 'Interface Countable correctly implemented');

        $ps2 = new PositionCollection();
        $ps2->addXY(1, 5);
        $ps2->addXY(7, 5);
        $ps->merge($ps2);
        $ps->sort();
        $t->is(array_keys($ps->getArray()), array("[1|5]", "[4|6]", "[7|5]"), '->merge works');
        $t->is(count($ps), 3, 'Interface Countable correctly implemented');

        $ps->delXY(4, 6);
        $t->is(array_keys($ps->getArray()), array("[1|5]", "[7|5]"), '->delXY() removes item');
        $t->is(count($ps), 2, 'delXY() removes item and count matches');
    }
}
