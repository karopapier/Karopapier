<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 00:27
 */

namespace AppBundle\Modules\Karolenderblatt\Service;


use AppBundle\Modules\Karolenderblatt\ValueObject\RawKarolenderblatt;

class FileParser
{

    public function getRawFromFile($file)
    {
        $lines = file($file);

        $blaetter = [];

        foreach ($lines as $i => $line) {
            if (preg_match(
                '/<a name="(\d\d\d\d-\d\d-\d\d)"><\/a><span id=".*?----- <br\/><\/span>/',
                $line,
                $matches
            )) {
                $blattline = $lines[$i + 1];

                // Skip Sonderzitat
                if (preg_match('/.*Sonderzitat.*/', $blattline)) {
                    continue;
                }

                // Skip Karolenderblatt-Nachtrag:
                if (preg_match('/.*Karolenderblatt-Nachtrag:.*/', $blattline)) {
                    continue;
                }


                $posted = $matches[1];

                $blaetter[] = RawKarolenderblatt::createFromLines($posted, $blattline);
            }
        }

        return $blaetter;
    }
}