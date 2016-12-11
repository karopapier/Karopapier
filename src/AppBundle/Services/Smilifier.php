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

    public function smilify($content, $smileytype = "images", $picType = "quotes", $linkType = "quotes", $escapeAmp = true)
    {
        /*
        $content = str_replace('<', '&lt;', $content);
        $content = str_replace('>', '&gt;', $content);
        $content = str_replace('\"', '&quot;', $content);
        $content = str_replace("\\\'", "'", $content);
        $content = str_replace('"', '&quot;', $content);
        $content = str_replace("\n", '<BR>', $content);
        */

        /*
        $raw = preg_replace('/<IMG SRC=bilder\/smilies\/(.*?).gif title=(.*?)>/i', ':\\1:', $raw);
        $raw = preg_replace('/<IMG SRC="bilder\/smilies\/(.*?).gif" title=(.*?)>/i', ':\\1:', $raw);
        $raw = preg_replace('/<IMG SRC="bilder\/smilies\/(.*?).gif" title="(.*?)">/i', ':\\1:', $raw);
        $raw = preg_replace('/<IMG SRC="bilder\/smilies\/(.*?).gif" alt="(.*?)" title="(.*?)">/', ':\\1:', $raw);
        */
        $smileycode = array(
                "legacy" => function ($smiley) {
                    return '<IMG SRC=bilder/smilies/' . $smiley . '.gif title=' . $smiley . '>';
                },
                "quotesnoalt" => function ($smiley) {
                    return '<IMG SRC="bilder/smilies/' . $smiley . '.gif" title=' . $smiley . '>';
                },
                "images" => function ($smiley) {
                    return '<img src="/images/smilies/' . $smiley . '.gif" alt="' . $smiley . '" title="' . $smiley . '">';
                },
        );

        $piccode = array(
                "legacy" => function ($src) {
                    return '<IMG SRC=' . $src . '>';
                },
                "quotesnoslash" => function ($src) {
                    return '<img src="' . $src . '">';
                },
                "quotes" => function ($src) {
                    return '<img src="' . $src . '" />';
                },
        );

        $linkcode = array(
                "quoteless" => function ($url, $text) {
                    return "<a href=$url>$text</a>";
                },
                "quotes" => function ($url, $text) {
                    return '<a href="' . $url . '">' . $text . '</a>';
                },
        );

        //$content = htmlentities($content);
        $content = htmlspecialchars($content);
        //re-replace amps
        if (!$escapeAmp) {
            $content = str_replace('&amp;', '&', $content);
        }


        // if there are two : at all...
        if (preg_match(' /:.*:/', $content)) {
            $smilies = $this->smileyHolder->getSmilies();
            foreach ($smilies as $smiley) {
                $func = $smileycode[$smileytype];
                $content = str_replace(':' . $smiley . ':', $func($smiley), $content);
            }
        }

        $textparts = explode(":-", $content);
        $picfunc = $piccode[$picType];
        $linkfunc = $linkcode[$linkType];
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
                if (preg_match("/-:Link text=(.*?) url=(.*?) Link:-/", $value, $matches)) {
                    $text = $matches[1];
                    $url = $matches[2];
                    $value = str_ireplace('-:Link text=' . $text . ' url=' . $url . ' Link:-', $linkfunc($url, $text), $value);
                }

                if (preg_match("/-:Pic src=(.*) Pic:-/", $value, $matches)) {
                    $src = $matches[1];
                    $value = str_ireplace('-:Pic src=' . $src . ' Pic:-', $picfunc($src), $value);
                }

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
    public function guessRaw($text, $escapedAmp = true)
    {
        $raw = $text;
        $raw = str_replace("&nbsp;&nbsp;", "  ", $raw);
        $raw = preg_replace('/ <BR>$/', '', $raw);

        //find links
        $raw = preg_replace('/<a href="(.*?)">(.*?)<\/a>/', '-:Link text=\\2 url=\\1 Link:-', $raw);
        $raw = preg_replace('/<A HREF="(.*?)">(.*?)<\/A>/', '-:Link text=\\2 url=\\1 Link:-', $raw);
        $raw = preg_replace('/<a href=(.*?)>(.*?)<\/a>/', '-:Link text=\\2 url=\\1 Link:-', $raw);
        $raw = preg_replace('/<A HREF=(.*?)>(.*?)<\/A>/', '-:Link text=\\2 url=\\1 Link:-', $raw);

        //find F, K, RED
        $raw = str_ireplace("<B>", "-:F", $raw);
        $raw = str_ireplace("</B>", "F:-", $raw);
        $raw = str_ireplace("<I>", "-:K", $raw);
        $raw = str_ireplace("</I>", "K:-", $raw);
        $raw = preg_replace('/<font color=#CC0000>/i', '-:RED', $raw);
        $raw = preg_replace('/<\/font>/i', 'RED:-', $raw);

        //find smiley
        $raw = preg_replace('/<img src="\/images\/smilies\/(.*?).gif" alt="(.*?)" title="(.*?)">/', ':\\1:', $raw);
        $raw = preg_replace('/<IMG SRC=bilder\/smilies\/(.*?).gif title=(.*?)>/i', ':\\1:', $raw);
        $raw = preg_replace('/<IMG SRC="bilder\/smilies\/(.*?).gif" title=(.*?)>/i', ':\\1:', $raw);
        $raw = preg_replace('/<IMG SRC="bilder\/smilies\/(.*?).gif" title="(.*?)">/i', ':\\1:', $raw);
        $raw = preg_replace('/<IMG SRC="bilder\/smilies\/(.*?).gif" alt="(.*?)" title="(.*?)">/', ':\\1:', $raw);

        //find pics
        $raw = preg_replace('/<IMG SRC="(.*?)">/', '-:Pic src=\\1 Pic:-', $raw);
        $raw = preg_replace('/<img src="(.*?)" \/>/', '-:Pic src=\\1 Pic:-', $raw);
        $raw = preg_replace('/<IMG SRC=(.*?)>/', '-:Pic src=\\1 Pic:-', $raw);

        if (!$escapedAmp) {
            $raw = str_ireplace('&', '&amp;', $raw);
        }
        $raw = html_entity_decode($raw);

        //$raw = preg_replace("/-:Pic src=(.*) Pic:-/", "<img src=\"\\1\" />", $value);
        return trim($raw);
    }
}