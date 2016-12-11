<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 09.12.2016
 * Time: 22:08
 */

namespace AppBundle\Services;

use Psr\Log\LoggerInterface;

class Smilifier
{
    /** @var SmileyHolderInterface $smileyHolder */
    private $smileyHolder;

    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct(SmileyHolderInterface $smileyHolder, LoggerInterface $logger)
    {
        $this->smileyHolder = $smileyHolder;
        $this->logger = $logger;
    }

    public function smilify($content)
    {
        /*
        $content = str_replace('<', '&lt;', $content);
        $content = str_replace('>', '&gt;', $content);
        $content = str_replace('\"', '&quot;', $content);
        $content = str_replace("\\\'", "'", $content);
        $content = str_replace('"', '&quot;', $content);
        $content = str_replace("\n", '<BR>', $content);
        */

        $content = htmlentities($content);

        // if there are two : at all...
        if (preg_match('/:.*:/', $content)) {
            $smilies = $this->smileyHolder->getSmilies();
            foreach ($smilies as $smiley) {
                $content = str_replace(':' . $smiley . ':', '<img src="/images/smilies/' . $smiley . '.gif" alt="' . $smiley . '" title="' . $smiley . '">', $content);
            }
        }

        $textparts = explode(":-", $content);
        if (count($textparts) > 1) {
            $content = "";
            for ($i = 0; $i < count($textparts) - 1; $i++) {
                $value = $textparts[$i];
                $value = $value . ":-";
                #echo "VALUE: $value<BR>";
                $value = str_replace(":-F:", "<b>", $value);
                $value = str_replace(":F-:", "</b>", $value);

                $value = str_replace("-:RED", "<font color=#CC0000>", $value);
                $value = str_replace("RED:-", "</font>", $value);

                $value = str_replace("-:K", "<i>", $value);
                $value = str_replace("K:-", "</i>", $value);

                $value = str_replace("-:F", "<b>", $value);
                $value = str_replace("F:-", "</b>", $value);
                $value = str_replace("-:B", "<b>", $value);
                $value = str_replace("B:-", "</b>", $value);
                $value = str_replace(":-Link:", "</a>", $value);
                $value = preg_replace("/-:Link text=(.*) url=(.*) Link:-/", "<a href=\"\\2\">\\1</a>", $value);
                $value = preg_replace("/-:Pic src=(.*) Pic:-/", "<img src=\"\\1\" />", $value);
                $value = str_replace("  ", "&nbsp;&nbsp;", $value);
                $content = $content . $value;
            }
            $content = $content . array_pop($textparts);
        }
        return $content;
    }

    /**
     * Try to guess the original "raw" string that the user entered that was converted to the given string
     * @param string $text
     * @return string
     */
    public function guessRaw($text)
    {
        $raw = $text;
        $raw = str_replace('<BR>', '', $raw);

        //find links
        $raw = preg_replace('/<a href="(.*?)">(.*?)<\/a>/', '-:Link text=\\2 url=\\1 Link:-', $raw);

        //find pics
        $raw = preg_replace('/<img src="(.*?)" \/>/', '-:Pic src=\\1 Pic:-', $raw);

        //find F, K, RED
        $raw = str_ireplace("<B>", "-:F", $raw);
        $raw = str_ireplace("</B>", "F:-", $raw);
        $raw = str_ireplace("<I>", "-:K", $raw);
        $raw = str_ireplace("</I>", "K:-", $raw);
        $raw = preg_replace('/<font color=#CC0000>/i', '-:RED', $raw);
        $raw = preg_replace('/<\/font>/i', 'RED:-', $raw);

        //find smiley
        $raw = preg_replace('/<img src="\/images\/smilies\/(.*?).gif" alt="(.*?)" title="(.*?)">/', ':\\1:', $raw);
        $raw = preg_replace('/<IMG SRC="bilder\/smilies\/(.*?).gif" alt="(.*?)" title="(.*?)">/', ':\\1:', $raw);

        //$raw = preg_replace("/-:Pic src=(.*) Pic:-/", "<img src=\"\\1\" />", $value);
        return trim($raw);
    }
}