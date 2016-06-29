<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 27.06.2016
 * Time: 20:20
 */

namespace AppBundle\Validator\Constraints\Mapcode;


$COUNT = 0;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FinishableValidator extends ConstraintValidator
{
    private $accessibility = array();
    private $queue = array();
    private $code;
    private $lines;
    private $rows;
    private $cols;
    private $cps;
    private $roadField;

    private function addErr($msg, $value)
    {
        $this->context->buildViolation($msg)
                ->setParameter('%string%', $value)
                ->addViolation();
    }

    public function validate($value, Constraint $constraint)
    {
        $this->code = str_replace("\r", "", $value);
        $this->lines = explode("\n", $value);
        $this->rows = count($this->lines);
        $this->cols = strlen($this->lines[0]);

        $this->roadField = '/[OSF\d]/';

        //Checkpoints list
        $chars = count_chars($value, 3);
        //only ints
        $ints = preg_replace('/[^0-9]+/', '', $chars);
        //split and sort them
        $this->cps = array();
        if ($ints != "") {
            $this->cps = str_split($ints);
        }

        $this->checkAccessibility();

        //check F to be accessible
        $finishes = $this->getAllCoordsOf("F");
        if (!$this->isOneAccessible($finishes)) {
            $this->addErr("Kann keines der Ziele erreichen", $value);
        }

        //check each cp group to be accessible
        foreach ($this->cps as $cp) {
            $coords = $this->getAllCoordsOf($cp);
            if (!$this->isOneAccessible($coords)) {
                $this->addErr("Kann keinen der Checkpoints Nr. " . $cp . " erreichen", $value);
            }
        }
    }

    private function isOneAccessible($coords)
    {
        foreach ($coords as $coord) {
            if ($this->isAccessible($coord)) return true;
        }
        return false;
    }

    private function checkAccessibility()
    {
        //put all starts on queue and then start checking
        $starts = $this->getAllCoordsOf("S");
        $this->enqueue($starts);

        while ($this->queueFilled()) {
            $this->next();
        }
    }

    private function getPosFromRowCol($r, $c)
    {
        return ($r * $this->cols) + $c;
    }

    private function getCoordForPos($pos)
    {
        $cols = $this->cols;
        $c = $pos % $cols;
        $r = floor($pos / $cols);
        return $c . '|' . $r;
    }

    private function getFieldAtRowCol($r, $c)
    {
        $pos = $this->getPosFromRowCol($r, $c);
        return substr($this->code, $pos, 1);
    }

    private function getAllCoordsOf($s)
    {
        preg_match_all("/" . $s . "/", $this->code, $matches, PREG_OFFSET_CAPTURE);
        $coords = array();
        foreach ($matches[0] as $match) {
            $coords[] = $this->getCoordForPos($match[1]);
        }
        return $coords;
    }

    private function queueFilled()
    {
        return (count($this->queue) > 0);
    }

    private function enqueue($v)
    {
        if (is_array($v)) {
            foreach ($v as $coord) {
                $this->queue[$coord] = $coord;
            }
            return;
        }

        $this->queue[$v] = $v;
    }

    private function next()
    {
        global $COUNT;
        $coord = array_shift($this->queue);
        $this->checkCoord($coord);
        $COUNT++;
        if ($COUNT > 5000) die();
    }

    private function checkCoord($coord)
    {
        //Already done?
        if (isset($this->accessibility[$coord])) return;

        if ($this->isRoad($coord)) {
            $this->accessibility[$coord] = true;
            list($x, $y) = explode("|", $coord);
            //check 8 surroundings
            for ($xi = -1; $xi <= 1; $xi++) {
                for ($yi = -1; $yi <= 1; $yi++) {
                    $nx = $x + $xi;
                    $ny = $y + $yi;
                    if (($nx >= 0) && ($nx < $this->cols)) {
                        if (($ny >= 0) && ($ny < $this->rows)) {
                            $nc = $nx . "|" . $ny;
                            if (!(isset($this->accessibility[$nc]))) {
                                $this->enqueue($nc);
                            }
                        }
                    }
                }
            }
            return;
        }
        $this->accessibility[$coord] = false;
    }

    private function getFieldAtCoord($coord)
    {
        list($x, $y) = explode("|", $coord);
        return $this->getFieldAtRowCol($y, $x);
    }

    private function isRoad($coord)
    {
        $field = $this->getFieldAtCoord($coord);
        return preg_match($this->roadField, $field);
    }

    private function isAccessible($coord)
    {
        if (isset($this->accessibility[$coord])) {
            return $this->accessibility[$coord];
        }
        return false;
    }


}