<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 25.10.2017
 * Time: 12:42
 */

namespace AppBundle\Serializer;

use AppBundle\Entity\Game;
use AppBundle\Entity\Map;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GameNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        if (!$this->supportsNormalization($object)) {
            throw new InvalidArgumentException('Not supported');
        }

        /** @var Map $map */
        $map = $object->getMap();

        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'map' => [
                'id' => $map->getId(),
                'code' => $map->getCode(),
            ],
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Game;
    }
}