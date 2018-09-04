<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 25.10.2017
 * Time: 12:42
 */

namespace AppBundle\Serializer;

use AppBundle\Entity\Move;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class MoveNormalizer implements NormalizerInterface
{
    public function normalize($move, $format = null, array $context = array())
    {
        if (!$this->supportsNormalization($move)) {
            throw new InvalidArgumentException('Not supported');
        }

        /** @var Move $move */
        $data = [
            'x' => $move->getXPos(),
            'y' => $move->getYPos(),
            'xv' => $move->getXVec(),
            'yv' => $move->getYVec(),
            't' => $move->getDate()->format('Y-m-d H:i:s'),
        ];
        if ($move->getMovemessage() !== '') {
            $data['msg'] = $move->getMovemessage();
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Move;
    }
}