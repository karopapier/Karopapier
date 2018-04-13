<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 18.07.2016
 * Time: 23:57
 */

namespace AppBundle\Twig;


class DateExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
                new \Twig_SimpleFilter('daysAgo', array($this, 'daysAgo')),
        );
    }

    public function daysAgo($date)
    {
        if (!$date) return -1;
        /** @var \DateTime $date */
        return floor((time() - $date->getTimestamp()) / 86400);
    }

    public function getName()
    {
        return 'date_extension';
    }
}