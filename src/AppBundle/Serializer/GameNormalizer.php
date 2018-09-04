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
    public function normalize($game, $format = null, array $context = array())
    {
        if (!$this->supportsNormalization($game)) {
            throw new InvalidArgumentException('Not supported');
        }

        $options = [
            'mapcode' => false,
            'players' => false,
            'moves' => false,
        ];

        foreach ($options as $option => $value) {
            if (array_key_exists($option, $context)) {
                $options[$option] = (bool)$context[$option];
            }
        }

        /** @var Map $map */
        $map = $game->getMap();

        $data = [
            'id' => $game->getId(),
            'name' => $game->getName(),
            'map' => [
                'id' => $map->getId(),
                'name' => $map->getName(),
            ],
        ];

        if ($options['mapcode']) {
            $data['map']['code'] = $map->getCode();
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Game;
    }
}