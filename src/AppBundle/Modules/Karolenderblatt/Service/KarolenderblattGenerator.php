<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 00:32
 */

namespace AppBundle\Modules\Karolenderblatt\Service;


use AppBundle\Entity\KarolenderBlatt;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;

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
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(FileParser $fileParser, KarolenderblattNormalizer $normalizer, ObjectManager $manager)
    {
        $this->fileParser = $fileParser;
        $this->normalizer = $normalizer;
        $this->manager = $manager;
    }

    public function generate()
    {
        // remove all previous
        $connection = $repo = $this->manager->getConnection();
        /** @var  Connection $connection */
        $connection->executeQuery('DELETE from karo_karolenderblatt');

        $blaetter = $this->fileParser->getRawFromFile('http://karolenderblatt.de');
        $sorted = [];
        $bulk = 0;
        foreach ($blaetter as $blatt) {
            /** @var KarolenderBlatt $karolenderblatt */
            $karolenderblatt = $this->normalizer->normalize($blatt);

            $this->manager->persist($karolenderblatt);
            $bulk++;
            if ($bulk > 50) {
                $this->manager->flush();
                $bulk = 0;
            }
        }

        $this->manager->flush();
    }
}