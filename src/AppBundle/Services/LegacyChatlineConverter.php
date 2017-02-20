<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 24.01.2017
 * Time: 17:22
 */

namespace AppBundle\Services;


use AppBundle\Entity\ChatMessage;

class LegacyChatlineConverter
{
    /** @var Smilifier */
    private $smilifier;

    public function __construct(Smilifier $smilifier)
    {
        $this->smilifier = $smilifier;
    }

    public function toLegacyChatline($login, $time, $text)
    {
        $text = $this->smilifier->smilify($text);
        return sprintf("<B>%s</B> (%s): %s <BR>\n", $login, $time, $text);
    }

    public function parseLegacyChatline($line)
    {
        if (substr($line, 0, 5) === '-----') {
            return array(
                    "login" => "",
                    "time" => "00:00",
                    "text" => $line
            );
        }

        if (!preg_match('/^<B>(.*?)<\/B> \((.*?)\): (.*?)<BR>/', $line, $matches)) {
            return array();
        }

        $text = $matches[3];
        $text = trim($text);

        $data = array(
                "login" => $matches[1],
                "time" => $matches[2],
                "text" => $text
        );

        return $data;
    }
}