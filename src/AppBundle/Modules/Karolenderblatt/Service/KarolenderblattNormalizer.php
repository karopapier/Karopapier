<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 00:20
 */

namespace AppBundle\Modules\Karolenderblatt\Service;


use AppBundle\Modules\Karolenderblatt\ValueObject\KarolenderBlatt;
use AppBundle\Modules\Karolenderblatt\ValueObject\RawKarolenderblatt;

class KarolenderblattNormalizer
{

    public function normalize(RawKarolenderblatt $blatt)
    {
        $line = $blatt->getLine();


        // Fixes/Korrekturen

        //2014-07-14
        $line = str_replace(
            'Karolenderblatt: mr-burns123, ultimate und Calypso fuehren ',
            'Heute vor einem Jahren fuehren mr-burns123, ultimate und Calypso ',
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


        var_dump($line);

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
        $line = str_ireplace('vor vier Jahren', 'vor 4 Jahren', $line);
        $line = str_ireplace('vor fuenf Jahren', 'vor 5 Jahren', $line);
        $line = str_ireplace('vor fuend Jahren', 'vor 5 Jahren', $line);
        $line = str_ireplace('vor sechs Jahren', 'vor 6 Jahren', $line);
        $line = str_ireplace('vor sieben Jahren', 'vor 7 Jahren', $line);
        $line = str_ireplace('vor acht Jahren', 'vor 8 Jahren', $line);
        $line = str_ireplace('vor neun Jahren', 'vor 9 Jahren', $line);
        $line = str_ireplace('vor neuen Jahren', 'vor 9 Jahren', $line);
        $line = str_ireplace('vor zehn Jahren', 'vor 10 Jahren', $line);

        $pattern = '/vor (\d+) Jahren/';
        if (preg_match($pattern, $line, $matches)) {
            $years = $matches[1];

            $normLine = preg_replace($pattern, 'vor {DIFF} Jahren', $line);
            $normLine = preg_replace(
                '/kili \((.*)\): Karolenderblatt:( |heute|gestern|vorgestern) vor/i',
                'Heute vor',
                $normLine
            );
            $normLine = preg_replace(
                '/kili \((.*)\): Karolenderblatt \(nachge(reicht|liefert)\): (heute|gestern|vorgestern) vor/i',
                'Heute vor',
                $normLine
            );
        } else {
            var_dump($blatt);
            die ('Weiss nicht wann - Jahre');
        }

        $posted = $blatt->getPosted();
        $d = \DateTime::createFromFormat("Y-m-d", $posted);
        $d->sub(new \DateInterval("P".$daymod."D"));
        $d->sub(new \DateInterval("P".$years."Y"));

        $k = KarolenderBlatt::create($posted, $d->format('Y-m-d'), $normLine);
        var_dump($k);

        return $k;
    }
}