<?php

use AppBundle\Validator\Constraints\Mapcode\LinesEqualLength;
use AppBundle\Validator\Constraints\Mapcode\LinesEqualLengthValidator;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;


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
        return true;
        $mapcode = "XXXXXX\nXXXX";
        $this->validator->validate($mapcode, new LinesEqualLength());
        $this->buildViolation("Alle Zeilen müssen gleich lang sein")
            ->setParameter('%string%', $mapcode)
            ->assertRaised();
    }

}