<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 00:32
 */

namespace AppBundle\Modules\Karolenderblatt\Service;


class KarolenderblattGenerator
{

    /**
     * @var FileParser
     */
    private $fileParser;
    /**
     * @var KarolenderblattNormalizer
     */
    private $normalizer;

    public function __construct(FileParser $fileParser, KarolenderblattNormalizer $normalizer)
    {
        $this->fileParser = $fileParser;
        $this->normalizer = $normalizer;
    }

    public function generate()
    {
        #$blaetter = $this->fileParser->getRawFromFile('http://karolenderblatt.de');
        $blaetter = $this->fileParser->getRawFromFile('/home/pdietrich/karopapier.de/wrapper/index.html');
        foreach ($blaetter as $blatt) {
            $karolenderblatt = $this->normalizer->normalize($blatt);
            var_dump($karolenderblatt);
        }
    }
}