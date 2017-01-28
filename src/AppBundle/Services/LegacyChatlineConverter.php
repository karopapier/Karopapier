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

    public function toLegacyChatline(ChatMessage $chatMessage)
    {
        $text = $this->smilifier->smilify($chatMessage->getText());
        $login = $chatMessage->getLogin();
        $time = $chatMessage->getTime();
        return sprintf("<B>%s</B> (%s): %s <BR>\n", $login, $time, $text);
    }

    public function parseLegacyChatline($line)
    {

        if (!preg_match('/^<B>(.*?)<\/B> \((.*?)\): (.*?) <BR>/', $line, $matches)) {
            return array();
        }

        $data = array(
                "login" => $matches[1],
                "time" => $matches[2],
                "text" => $matches[3],
        );

        return $data;
    }
}