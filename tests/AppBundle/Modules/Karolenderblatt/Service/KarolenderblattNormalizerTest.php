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
            '<b>kili</b> (11:12): Karolenderblatt: Heute vor 4 Jahren hat Jody im Karopapierchat darauf gewettet, dass Deutschland nicht gewinnt. <br/>'
        );

        $normalizer = new KarolenderblattNormalizer();
        $blatt = $normalizer->normalize($raw);

        $this->assertEquals($this->getDateTime('2014-07-06'), $blatt->getPostedDate(), 'posted date is kept');
        $this->assertEquals(
            $this->getDateTime('2010-07-06'),
            $blatt->getEventDate(),
            'event date is calculated correctly'
        );
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

        $this->assertEquals($this->getDateTime('2014-07-23'), $blatt->getPostedDate(), 'posted date is kept');
        $this->assertEquals(
            $this->getDateTime('2005-07-22'),
            $blatt->getEventDate(),
            'event date is calculated correctly'
        );
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

        $this->assertEquals($this->getDateTime('2014-09-07'), $blatt->getPostedDate(), 'posted date is kept');
        $this->assertEquals(
            $this->getDateTime('2006-09-05'),
            $blatt->getEventDate(),
            'event date is calculated correctly'
        );
        $this->assertEquals(
            'Heute vor {DIFF} Jahren spielt Didi alles kaputt (O-Ton quabla), baut dafuer aber ein, dass man bei angeschalteten Checkpoints ueber Startfelder ins Ziel fahren kann.',
            $blatt->getLine(),
            'Line is converted correctly'
        );
    }

    public function testOhneHeuteBlatt()
    {
        $raw = RawKarolenderblatt::createFromLines(
            '2014-07-18',
            'kili (21:07): Karolenderblatt: vor einem Jahr erreicht der Mapclickcounter den Wert 42195700. Lt. mr-burns123 ist das ein Marathon in Millimetern. '
        );
        $normalizer = new KarolenderblattNormalizer();
        $blatt = $normalizer->normalize($raw);

        $this->assertEquals($this->getDateTime('2014-07-18'), $blatt->getPostedDate(), 'posted date is kept');
        $this->assertEquals(
            $this->getDateTime('2013-07-18'),
            $blatt->getEventDate(),
            'event date is calculated correctly'
        );
        $this->assertEquals(
            'Heute vor {DIFF} Jahren erreicht der Mapclickcounter den Wert 42195700. Lt. mr-burns123 ist das ein Marathon in Millimetern.',
            $blatt->getLine(),
            'Line is converted correctly'
        );

    }

    public function testCommentedBlatt()
    {
        $raw = RawKarolenderblatt::createFromLines(
            '2014-07-23',
            '<b>kili</b> (23:53): Karolenderblatt (auf den letzten Druecker dafuer aber total relevant): Heute vor fuenf Jahren stellt Dave223 eine neue Karte vor; http://wiki.karopapier.de/Karte:_karostreckenbaustelle  <br/>            '
        );
        $normalizer = new KarolenderblattNormalizer();
        $blatt = $normalizer->normalize($raw);

        $this->assertEquals($this->getDateTime('2014-07-23'), $blatt->getPostedDate(), 'posted date is kept');
        $this->assertEquals(
            $this->getDateTime('2009-07-23'),
            $blatt->getEventDate(),
            'event date is calculated correctly'
        );
        $this->assertEquals(
            'Heute vor {DIFF} Jahren stellt Dave223 eine neue Karte vor; http://wiki.karopapier.de/Karte:_karostreckenbaustelle',
            $blatt->getLine(),
            'Line is converted correctly'
        );

    }

    private function getDateTime($datestring)
    {
        return \DateTime::createFromFormat('Y-m-d', $datestring);
    }
}