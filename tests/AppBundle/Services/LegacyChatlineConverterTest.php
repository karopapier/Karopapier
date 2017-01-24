<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 24.01.2017
 * Time: 17:26
 */

namespace tests\AppBundle\Services;


use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\User;
use AppBundle\Services\LegacyChatlineConverter;
use AppBundle\Services\SmileyHolder;
use AppBundle\Services\Smilifier;


class LegacyChatlineConverterTest extends \PHPUnit_Framework_TestCase
{

    public function testConversion()
    {
        $smilfier = $this->getMockBuilder(Smilifier::class)->disableOriginalConstructor()->getMock();
        $smilfier->expects($this->once())
                ->method('smilify')
                ->will($this->returnValue("Lala"));

        $user = $this->getMock(User::class);
        $user->expects($this->once())
                ->method('getUsername')
                ->will($this->returnValue("Diadia"));

        $converter = new LegacyChatlineConverter($smilfier);

        $time = time();
        $cm = new ChatMessage($user, "Lala");

        $line = $converter->toLegacyChatline($cm);
        $expected = "Diadia (" . date("H:i", $time) . "): Lala <BR>\n";

        $this->assertEquals($line, $expected, "returns old school line");

    }

}
