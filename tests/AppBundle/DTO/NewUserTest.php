<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 02.11.2017
 * Time: 23:32
 */

namespace AppBundle\Tests;

use AppBundle\DTO\NewUser;
use Symfony\Component\Validator\Validation;

class NewUserTest extends \PHPUnit_Framework_TestCase
{

    public function testLoginAssertion()
    {
        $usernames = array(
            'תהו' => false,
            'Derwodernameisteinfachvielzulang' => false,
            'zk' => false,
            'ÜmläütT€ßtÜsér' => false,
            '-D0G-' => true,
            '0n3w0rld' => true,
            '100wasser' => true,
            '1malDidiaergern' => true,
            ';Philipp' => true,
            '==PartyA' => true,
            '=quiEtscHEEntch' => true,
            '????' => true,
            'a.lo' => true,
            'Automatikk2' => true,
            'BassBoostinator' => true,
            'Bl4ckfir3' => true,
            'Bl@ckSp@rk' => true,
            'chris.Ytterbium' => true,
            'CoCa Mike' => true,
            'Dahollända' => true,
            'Daniel____' => true,
            'dE KuYp3r' => true,
            'Der heilige Geis' => true,
            'Ein Dude kommt selte' => true,
            'HansJoachimloewenzah' => true,
            'Kindergärtnerin' => true,
            'liebergehenalsfahren' => true,
            'ma(V3)xx' => true,
            'Müllauto' => true,
            'März-edes-CLK' => true,
            'NICHT EINLADEN' => true,
            'Schüler05' => true,
            '[lunatic]suicide' => true,
            'äöüßÄÖÜ' => true,
            'ödipups' => true,
            'Örni' => true,
            '_ESKAL' => true,
        );

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();

        foreach ($usernames as $username => $expectedAsValid) {
            $u = new NewUser($username);
            $errorsExpected = $expectedAsValid ? 0 : 1;

            $errors = $validator->validate($u);
            $this->assertEquals($errorsExpected, count($errors), "Name ".$username);
        }
    }

}
