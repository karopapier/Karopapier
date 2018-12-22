<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 21.12.2018
 * Time: 00:41
 */

namespace AppBundle\Modules\Games\DTO;


use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints as Assert;

class GameFilterSettings
{
    /**
     * @var string
     * @Assert\Regex("/^[a-zA-z0-9§$ ]*$/")
     */
    public $name = '';

    public static function createFromParameters(ParameterBag $bag)
    {
        $filter = new self();

        $filter->name = $bag->getAlnum('name', '');

        return $filter;
    }
}