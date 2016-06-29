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

class LinesEqualLengthValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        $value = str_replace("\r", "", $value);
        $lines = explode("\n", $value);
        $lengths = array_map("strlen", $lines);
        //expect 1 unique length value
        $c = count(array_unique($lengths));
        if ($c > 1) {
            $this->context->buildViolation($constraint->message)
                    ->setParameter('%string%', $value)
                    ->addViolation();
        }
    }
}