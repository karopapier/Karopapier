<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 25.06.2016
 * Time: 19:46
 */

namespace AppBundle\Model;


abstract class BaseMap
{
    public function getNbCols()
    {
        $matrix = $this->getMatrix();
        return strlen($matrix[0]);
    }

    public function getNbRows()
    {
        $matrix = $this->getMatrix();
        return count($matrix);
    }

    public function getMatrix()
    {
        if (isset($this->matrix)) {
            return $this->matrix;
        }

        $matrix = array();
        $code = $this->getCode();
        $lines = explode("\n", $code);
        foreach ($lines as $line) {
            $matrix[] = $line;
        }
        $this->matrix = $matrix;
        return $matrix;
    }

}