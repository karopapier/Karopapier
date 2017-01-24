<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 24.01.2017
 * Time: 17:13
 */

namespace tests\AppBundle\Services;


use AppBundle\Entity\Message;
use AppBundle\Services\MessageNormalizer;


class MessageNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalization()
    {
        $norm = new MessageNormalizer();
        $normalizations = array(
                '  asd f  ' => 'asd f',
        );
        foreach ($normalizations as $input => $expected) {
            $actual = $norm->normalize($input);
            $this->assertEquals($expected, $actual);
        }
    }
}
