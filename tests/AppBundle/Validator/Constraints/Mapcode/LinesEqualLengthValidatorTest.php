<?php

use AppBundle\Validator\Constraints\MapcodeConstraint\LinesEqualLength;
use AppBundle\Validator\Constraints\MapcodeConstraint\LinesEqualLengthValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;


/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 27.06.2016
 * Time: 20:29
 */
class LinesEqualLengthValidatorTest extends ConstraintValidatorTestCase
{

    protected function createValidator()
    {
        return new LinesEqualLengthValidator();
    }

    public function testValidationForDifferentLength()
    {
        $mapcode = "XXX\nXXXX";
        $this->validator->validate($mapcode, new LinesEqualLength());
        $this->buildViolation("Alle Zeilen müssen gleich lang sein")
            ->setParameter('%string%', $mapcode)
            ->assertRaised();

        return true;
    }

}