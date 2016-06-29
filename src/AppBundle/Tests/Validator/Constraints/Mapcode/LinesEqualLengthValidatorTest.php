<?php

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use  Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use AppBundle\Validator\Constraints\Mapcode\LinesEqualLength;
use AppBundle\Validator\Constraints\Mapcode\LinesEqualLengthValidator;

/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 27.06.2016
 * Time: 20:29
 */
class LinesEqualLengthValidatorTest extends AbstractConstraintValidatorTest
{

    protected function createValidator()
    {
        return new LinesEqualLengthValidator();
    }

    public function testValidationForDifferentLength()
    {
        $mapcode = "XXXXXX\nXXXX";
        $this->validator->validate($mapcode, new LinesEqualLength());
        $this->buildViolation("Alle Zeilen müssen gleich lang sein")
                ->setParameter('%string%', $mapcode)
                ->assertRaised();
    }

}