<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 10.12.2016
 * Time: 22:37
 */

namespace Tests\AppBundle\Services;


use AppBundle\Services\Smilifier;
use AppBundle\Services\SmileyHolder;


class SmilifierTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Smilifier $smilifier */
    private $smilifier;

    protected function setUp()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $smileyHolder = $this->getMock('AppBundle\Services\SmileyHolderInterface');
        $smileyHolder->expects($this->any())
                ->method('getSmilies')
                ->will($this->returnValue(array(
                        "approve",
                        "tongue",
                        "devilfire",
                        "unused"
                )));
        $this->smilifier = new Smilifier($smileyHolder, $logger);
    }

    public function testSmilifier()
    {
        $sm = $this->smilifier;
        $this->assertEquals('Hallo', $sm->smilify("Hallo"), 'unchanged');
        $this->assertEquals('Hallo <b>FETT</b>', $sm->smilify('Hallo -:FFETTF:-'), 'Fett');
        $this->assertEquals('Hallo <i>KURSIV</i>', $sm->smilify('Hallo -:KKURSIVK:-'), 'Kursiv');
        $this->assertEquals('Hallo <img src="/images/smilies/approve.gif" alt="approve" title="approve">', $sm->smilify('Hallo :approve:'), 'smiley to img');
        $this->assertEquals('Hallo :ismiregal: gibts nich', $sm->smilify('Hallo :ismiregal: gibts nich'), 'no change if smiley does not exist');

        $res = $sm->smilify(':tongue:');
        $exp = '<img src="/images/smilies/tongue.gif" alt="tongue" title="tongue">';
        $this->assertEquals($exp, $res, 'converts smilies');

        $res = $sm->smilify('http://');
        $exp = 'http://';
        $this->assertEquals($exp, $res, 'leaves unmatched stuff unchanged');

        $res = $sm->smilify('Hier ist ein -:Link text=beididi url=http://www.beididi.de Link:- im Text');
        $exp = 'Hier ist ein <a href="http://www.beididi.de">beididi</a> im Text';
        $this->assertEquals($exp, $res, 'converts links');

        $res = $sm->smilify('-:Pic src=http://www.brot.de/logo.gif Pic:- und -:Pic src=http://www.brot.de/logo.png Pic:- sind Bilder');
        $exp = '<img src="http://www.brot.de/logo.gif" /> und <img src="http://www.brot.de/logo.png" /> sind Bilder';
        $this->assertEquals($exp, $res, 'converts pics');

        $res = $sm->smilify('');
        $exp = '';
        $this->assertEquals($exp, $res, 'NULL is NULL');
    }

    public function testEscaping()
    {
        $sm = $this->smilifier;
        $this->assertEquals('&lt;SCRIPT&gt;', $sm->smilify("<SCRIPT>"), 'escape tags');
        $this->assertEquals('Wie schaut\'s aus? &quot;FETT?&quot;', $sm->smilify('Wie schaut\'s aus? "FETT?"'), 'escape tags');
    }

    public function testGuessRaw()
    {
        $sm = $this->smilifier;

        $line1 = '<IMG SRC="bilder/smilies/devilfire.gif" alt="devilfire" title="devilfire"> <font color=#CC0000> MUAAAHAHAHAHAHAHA </font> <IMG SRC="bilder/smilies/devilfire.gif" alt="devilfire" title="devilfire"> <BR>';
        $line2 = '<IMG SRC="bilder/smilies/devilfire.gif" alt="devilfire" title="devilfire"> <font color=#CC0000> <b> MUAAAAAHAHAHAHAHAH </b> </font> <IMG SRC="bilder/smilies/devilfire.gif" alt="devilfire" title="devilfire"> <BR>';
        $raw1 = ':devilfire: -:RED MUAAAHAHAHAHAHAHA RED:- :devilfire:';
        $raw2 = ':devilfire: -:RED -:F MUAAAAAHAHAHAHAHAH F:- RED:- :devilfire:';

        $this->assertEquals($raw1, $sm->guessRaw($line1), "guess raw of devils MUHAHA");
        $this->assertEquals($raw2, $sm->guessRaw($line2), "guess raw of other devils MUHAHA");

        $pic1 = '<img src="http://smile.welcomes-you.com/Unhappy/finger-020.gif" />';
        $rawpic1 = '-:Pic src=http://smile.welcomes-you.com/Unhappy/finger-020.gif Pic:-';
        $this->assertEquals($rawpic1, $sm->guessRaw($pic1), "guess raw of pic");

        $link1 = '<a href="http://www.karopapier.de/showmap.php?GID=94201">Direktlink_1.0</a>';
        $rawlink1 = '-:Link text=Direktlink_1.0 url=http://www.karopapier.de/showmap.php?GID=94201 Link:-';
        $this->assertEquals($rawlink1, $sm->guessRaw($link1), "guess raw of link");
    }

    public function testFullcircle()
    {
        /* Test if smilified guess is similar to original */
        $sm = $this->smilifier;

        $line = '<IMG SRC="bilder/smilies/devilfire.gif" alt="devilfire" title="devilfire"> <font color=#CC0000> MUAAAHAHAHAHAHAHA </font> <IMG SRC="bilder/smilies/devilfire.gif" alt="devilfire" title="devilfire">';
        $after = '<IMG SRC="/images/smilies/devilfire.gif" alt="devilfire" title="devilfire"> <font color=#CC0000> MUAAAHAHAHAHAHAHA </font> <IMG SRC="/images/smilies/devilfire.gif" alt="devilfire" title="devilfire">';
        $this->assertContains($after, $sm->smilify($sm->guessRaw($line)), 'guess + smilify = result', true);
    }
}
