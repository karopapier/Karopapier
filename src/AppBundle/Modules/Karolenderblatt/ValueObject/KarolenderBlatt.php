<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 01:12
 */

namespace AppBundle\Modules\Karolenderblatt\ValueObject;


class KarolenderBlatt
{
    private $eventDate;
    private $line;
    private $posted;

    private function __construct($posted, $eventDate, $line)
    {
        $this->eventDate = $eventDate;
        $this->line = $line;
        $this->posted = $posted;
    }

    public static function create($posted, $eventDate, $line)
    {
        return new self($posted, $eventDate, $line);
    }

    /**
     * @return mixed
     */
    public function getPosted()
    {
        return $this->posted;
    }

    /**
     * @return mixed
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return mixed
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }
}