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

    public function testYesterdayBlatt()
    {
        $raw = RawKarolenderblatt::createFromLines(
            '2014-07-23',
            'kili (23:43): Karolenderblatt (nachgeliefert): gestern vor neun Jahren wurde Incognitinchen morgens um 5:15 Uhr von ihren Meerschweinchen geweckt.'
        );

        $normalizer = new KarolenderblattNormalizer();
        $blatt = $normalizer->normalize($raw);

        $this->assertEquals('2014-07-23', $blatt->getPosted(), 'posted date is kept');
        $this->assertEquals('2005-07-22', $blatt->getEventDate(), 'event date is calculated correctly');
        $this->assertEquals(
            'Heute vor {DIFF} Jahren wurde Incognitinchen morgens um 5:15 Uhr von ihren Meerschweinchen geweckt.',
            $blatt->getLine(),
            'Line is converted correctly'
        );
    }

    public function testLateBlatt()
    {
        $raw = RawKarolenderblatt::createFromLines(
            '2014-09-07',
            'kili (20:21): Karolenderblatt (nachgereicht): vorgestern vor acht Jahren spielt Didi alles kaputt (O-Ton quabla), baut dafuer aber ein, dass man bei angeschalteten Checkpoints ueber Startfelder ins Ziel fahren kann.'
        );
        $normalizer = new KarolenderblattNormalizer();
        $blatt = $normalizer->normalize($raw);

        $this->assertEquals('2014-09-07', $blatt->getPosted(), 'posted date is kept');
        $this->assertEquals('2006-09-05', $blatt->getEventDate(), 'event date is calculated correctly');
        $this->assertEquals(
            'Heute vor {DIFF} Jahren spielt Didi alles kaputt (O-Ton quabla), baut dafuer aber ein, dass man bei angeschalteten Checkpoints ueber Startfelder ins Ziel fahren kann.',
            $blatt->getLine(),
            'Line is converted correctly'
        );
    }
}