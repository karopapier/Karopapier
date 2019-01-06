<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 00:07
 */

namespace AppBundle\Controller\Api;

use Symfony\Component\Routing\Annotation\Route;

class KarolenderblattController
{
    /**
     * @Route("/karolenderblatt/{y}-{m}-{d}", requirements={"y": "\d{4}","m": "\d{2}", "d": "\d{2}"})
     */
    public function get($y, $m, $d)
    {
        echo "DADA $y - $m - $d";
        die();
    }
}