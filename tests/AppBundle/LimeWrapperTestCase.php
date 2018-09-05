<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 05.09.2018
 * Time: 23:58
 */

namespace Tests\AppBundle;


use PHPUnit\Framework\TestCase;

class LimeWrapperTestCase extends TestCase
{
    public function is($a, $b, $c)
    {
        $this->assertEquals($a, $b, $c);
    }

    public function ok($a, $b)
    {
        $this->assertTrue($a, $b);
    }
}