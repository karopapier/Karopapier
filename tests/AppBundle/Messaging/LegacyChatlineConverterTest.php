<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 24.01.2017
 * Time: 17:26
 */

namespace tests\AppBundle\Services;


use AppBundle\Chat\LegacyChatlineConverter;
use AppBundle\Formatter\Smilifier;
use PHPUnit\Framework\TestCase;


/* Legacy throws warning of Symfony Bridge *shrug* */

class OldChatlineConverterTest extends TestCase
{

    /** @var  LegacyChatlineConverter */
    private $converter;

    public function testConversion()
    {

        $smilfier = $this->createMock(Smilifier::class);
        $smilfier->expects($this->once())
            ->method('smilify')
            ->willReturn("Was ne Nachricht...");

        $this->converter = new LegacyChatlineConverter($smilfier);

        $time = "12:34";
        $login = "Diadia";

        $line = $this->converter->toLegacyChatline($login, $time, "Egal, ruft smilifier");
        $expected = "<B>Diadia</B> (12:34): Was ne Nachricht... <BR>\n";

        $this->assertEquals($line, $expected, "returns old school line");

    }

    public function testParser()
    {
        $smilfier = $this->createMock(Smilifier::class);
        $this->converter = new LegacyChatlineConverter($smilfier);

        $expected = array(
            "<B>Didi</B> (13:51): LOS GEHT'S!!!! <BR>" => array(
                'login' => 'Didi',
                'time' => '13:51',
                'text' => "LOS GEHT'S!!!!",
            ),
        );

        foreach ($expected as $line => $result) {
            $this->assertEquals($result, $this->converter->parseLegacyChatline($line));
        }


    }

}
