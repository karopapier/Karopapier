<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 21.12.2018
 * Time: 00:41
 */

namespace AppBundle\Modules\Map\DTO;


use Symfony\Component\HttpFoundation\ParameterBag;

class MapFilter
{
    /**
     * @var string
     * @Assert
     */
    public $name = '';

    public static function createFromParameters(ParameterBag $bag)
    {
        $filter = new self();

        $filter->name = $bag->getAlnum('name', '');

        return $filter;
    }
}