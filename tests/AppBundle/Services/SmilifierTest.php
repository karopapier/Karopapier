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
                        "approve"
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
    }
}
