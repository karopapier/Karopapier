<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 00:20
 */

namespace AppBundle\Modules\Karolenderblatt\Service;


use AppBundle\Entity\KarolenderBlatt;
use AppBundle\Modules\Karolenderblatt\ValueObject\RawKarolenderblatt;

class KarolenderblattNormalizer
{

    /**
     * @param RawKarolenderblatt $blatt
     * @return KarolenderBlatt
     * @throws \Exception
     */
    public function normalize(RawKarolenderblatt $blatt)
    {
        $line = $blatt->getLine();
        $line = strip_tags($line);
        $line = trim($line);

        $line = str_replace('Reservekarolenderblatt', 'Karolenderblatt', $line);
        $line = str_replace('Karolenderblatt; ', 'Karolenderblatt: ', $line);
        $line = str_replace('Karolenderblatt heute vor', 'Karolenderblatt: Heute vor', $line);
        $line = str_replace('(nachgereicht) gestern', '(nachgereicht): gestern', $line);
        $line = str_replace('(nachgereicht) vorgestern', '(nachgereicht): vorgestern', $line);

        // Fixes/Korrekturen

        //2014-07-14
        $line = str_replace(
            'Karolenderblatt: mr-burns123, ultimate und Calypso fuehren ',
            'Karolenderblatt: Heute vor einem Jahren fuehren mr-burns123, ultimate und Calypso ',
            $line
        );

        // 2014-07-18
        $line = str_replace(
            'Karolenderblatt: vor einem Jahr',
            'Karolenderblatt: Heute vor einem Jahr',
            $line
        );

        // vom 2014-10-10 fuer 2005-10-07
        $line = str_replace(
            'Karolenderblatt (nachgereicht): am 07.10.2005',
            'Karolenderblatt (nachgereicht): Vorvorgestern vor neun Jahren',
            $line
        );

        // 2015-06-26
        $line = str_replace(
            'Karolenderblatt: heute vor neun Jahren verlinkt Madeleine ein Foto ihrer Katze',
            'Karolenderblatt: heute vor zehn Jahren verlinkt Madeleine ein Foto ihrer Katze',
            $line
        );

        // replace comments
        $commentPattern = '/Karolenderblatt (.*?): (heute|gestern|vorgestern|vorvorgestern)/i';
        if (preg_match($commentPattern, $line, $matches)) {
            $line = preg_replace($commentPattern, 'Karolenderblatt: '.$matches[2], $line);
        }

        $daymod = -1;
        if (preg_match('/heute vor/i', $line)) {
            $daymod = 0;
        }

        if (preg_match('/gestern vor/i', $line)) {
            $daymod = 1;
        }

        if (preg_match('/vorgestern vor/i', $line)) {
            $daymod = 2;
        }

        if (preg_match('/vorvorgestern vor/i', $line)) {
            $daymod = 3;
        }

        if ($daymod < 0) {
            var_dump($blatt);
            die('weiss nicht wann - Tage');
        }

        $line = str_ireplace('vor einem Jahr', 'vor 1 Jahren', $line);
        $line = str_ireplace('vor zwei Jahren', 'vor 2 Jahren', $line);
        $line = str_ireplace('vor drei Jahren', 'vor 3 Jahren', $line);
        $line = str_ireplace('vor dre[ Jahren', 'vor 3 Jahren', $line);
        $line = str_ireplace('vor frei Jahren', 'vor 3 Jahren', $line);
        $line = str_ireplace('vor vier Jahren', 'vor 4 Jahren', $line);
        $line = str_ireplace('vor fuenf Jahren', 'vor 5 Jahren', $line);
        $line = str_ireplace('vor fuend Jahren', 'vor 5 Jahren', $line);
        $line = str_ireplace('vor sechs Jahren', 'vor 6 Jahren', $line);
        $line = str_ireplace('vor sech Jahren', 'vor 6 Jahren', $line);
        $line = str_ireplace('vor sieben Jahren', 'vor 7 Jahren', $line);
        $line = str_ireplace('vor acht Jahren', 'vor 8 Jahren', $line);
        $line = str_ireplace('vor neun Jahren', 'vor 9 Jahren', $line);
        $line = str_ireplace('vor neuen Jahren', 'vor 9 Jahren', $line);
        $line = str_ireplace('vor zehn Jahren', 'vor 10 Jahren', $line);
        $line = str_ireplace('vor elf Jahren', 'vor 11 Jahren', $line);
        $line = str_ireplace('vor zwoelf Jahren', 'vor 12 Jahren', $line);
        $line = str_ireplace('vor dreizehn Jahren', 'vor 13 Jahren', $line);

        $pattern = '/vor (\d+) Jahren/';
        if (preg_match($pattern, $line, $matches)) {
            $years = $matches[1];

            $normLine = preg_replace($pattern, 'vor {DIFF} Jahren', $line);
            $normLine = preg_replace(
                '/kili \((.*)\): Karolenderblatt: (heute|gestern|vorgestern|vorvorgestern) vor/i',
                'Heute vor',
                $normLine
            );
            $normLine = preg_replace(
                '/kili \((.*)\): Karolenderblatt \(nachge(reicht|liefert)\): (heute|gestern|vorgestern|vorvorgestern) vor/i',
                'Heute vor',
                $normLine
            );
        } else {
            var_dump($blatt);
            die ('Weiss nicht wann - Jahre');
        }

        // Safety check, darf nicht mehr mit "kili: (xxxxx) anfangen"

        if (substr($normLine, 0, 4) === 'kili') {
            var_dump($normLine);
            die('Da ist noch kili uebrig');
        }

        $posted = $blatt->getPosted();
        $d = \DateTime::createFromFormat("Y-m-d", $posted);
        $d->sub(new \DateInterval("P".$daymod."D"));
        $d->sub(new \DateInterval("P".$years."Y"));

        return KarolenderBlatt::createFromStrings($posted, $d->format('Y-m-d'), $normLine);
    }
}