<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 00:21
 */

namespace Tests\AppBundle\Modules\Karolenderblatt\Service;


use AppBundle\Modules\Karolenderblatt\Service\KarolenderblattNormalizer;
use AppBundle\Modules\Karolenderblatt\ValueObject\RawKarolenderblatt;
use PHPUnit\Framework\TestCase;

class KarolenderblattNormalizerTest extends TestCase
{
    public function testTodayBlatt()
    {
        $raw = RawKarolenderblatt::createFromLines(
            '2014-07-06',
            'kili (11:12): Karolenderblatt: Heute vor 4 Jahren hat Jody im Karopapierchat darauf gewettet, dass Deutschland nicht gewinnt.'
        );

        $normalizer = new KarolenderblattNormalizer();
        $blatt = $normalizer->normalize($raw);

        $this->assertEquals('2014-07-06', $blatt->getPosted(), 'posted date is kept');
        $this->assertEquals('2010-07-06', $blatt->getEventDate(), 'event date is calculated correctly');
        $this->assertEquals(
            'Heute vor {DIFF} Jahren hat Jody im Karopapierchat darauf gewettet, dass Deutschland nicht gewinnt.',
            $blatt->getLine(),
            'Line is converted correctly'
        );
    }

}