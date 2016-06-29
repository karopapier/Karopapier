<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 27.06.2016
 * Time: 20:20
 */

namespace AppBundle\Validator\Constraints\Mapcode;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HasBorderValidator extends ConstraintValidator
{

    private function addErr($msg, $value)
    {
        $this->context->buildViolation($msg)
                ->setParameter('%string%', $value)
                ->addViolation();
    }

    public function validate($value, Constraint $constraint)
    {
        $lines = explode("\n", $value);
        $rows = count($lines);
        $cols = strlen($lines[0]);

        $field = '/[OSF\d]/';
        if (preg_match($field, $lines[0])) {
            $this->addErr("Rahmen oben ist nicht durchgängig", $value);
        }

        if (preg_match($field, $lines[$rows - 1])) {
            $this->addErr("Rahmen unten ist nicht durchgängig", $value);
        }

        $lefts = array_map(function ($l) {
            return substr($l, 0, 1);
        }, $lines);
        $leftletters = implode("", $lefts);
        if (preg_match($field, $leftletters)) {
            $this->addErr("Rahmen links ist nicht durchgängig", $value);
        }

        $rights = array_map(function ($l) {
            return substr($l, -1, 1);
        }, $lines);
        $rightletters = implode("", $rights);
        if (preg_match($field, $rightletters)) {
            $this->addErr("Rahmen rechts ist nicht durchgängig", $value);
        }
    }
}