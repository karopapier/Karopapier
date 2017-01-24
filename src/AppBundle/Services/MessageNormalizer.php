<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 24.01.2017
 * Time: 17:10
 */

namespace AppBundle\Services;


class MessageNormalizer
{

    public function normalize($text)
    {
        return trim($text);
    }

}