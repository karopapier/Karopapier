<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 27.06.2016
 * Time: 20:18
 */

namespace AppBundle\Validator\Constraints\MapcodeConstraint;


use Symfony\Component\Validator\Constraint;

/**
 * Class LinesEqualLength
 * @package AppBundle\Validator\MapcodeConstraint
 * @Annotation
 */
class LinesEqualLength extends Constraint
{
    public $message = "Alle Zeilen müssen gleich lang sein";

}