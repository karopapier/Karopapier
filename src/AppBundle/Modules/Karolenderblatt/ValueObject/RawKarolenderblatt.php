<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 00:25
 */

namespace AppBundle\Modules\Karolenderblatt\ValueObject;


class RawKarolenderblatt
{
    private $posted;
    private $line;

    private function __construct($posted, $line)
    {
        $this->posted = $posted;
        $this->line = $line;
    }

    public static function createFromLines($posted, $line)
    {
        return new self($posted, $line);
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
    public function getPosted()
    {
        return $this->posted;
    }
}