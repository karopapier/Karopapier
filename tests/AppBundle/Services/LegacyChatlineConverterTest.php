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

    /** @var  LegacyChatlineConverter */
    private $converter;

    public function testConversion()
    {

        $smilfier = $this->getMockBuilder(Smilifier::class)->disableOriginalConstructor()->getMock();
        $smilfier->expects($this->once())
                ->method('smilify')
                ->will($this->returnValue("Lala"));

        $this->converter = new LegacyChatlineConverter($smilfier);

        $time = time();
        $user = $this->getMock(User::class);
        $user->expects($this->once())
                ->method('getUsername')
                ->will($this->returnValue("Diadia"));
        $cm = new ChatMessage($user, "Lala");

        $line = $this->converter->toLegacyChatline($cm);
        $expected = "<B>Diadia</B> (" . date("H:i", $time) . "): Lala <BR>\n";

        $this->assertEquals($line, $expected, "returns old school line");

    }

    public function testParser()
    {
        $smilfier = $this->getMockBuilder(Smilifier::class)->disableOriginalConstructor()->getMock();
        $this->converter = new LegacyChatlineConverter($smilfier);

        $expected = array(
                "<B>Didi</B> (13:51): LOS GEHT'S!!!! <BR>" => array(
                        'login' => 'Didi',
                        'time' => '13:51',
                        'text' => "LOS GEHT'S!!!!"
                )
        );

        foreach ($expected as $line => $result) {
            $this->assertEquals($result, $this->converter->parseLegacyChatline($line));
        }


    }

}
