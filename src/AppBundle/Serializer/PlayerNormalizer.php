<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 25.10.2017
 * Time: 12:42
 */

namespace AppBundle\Serializer;

use AppBundle\Entity\Player;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class PlayerNormalizer implements NormalizerInterface
{
    public function normalize($player, $format = null, array $context = array())
    {
        if (!$this->supportsNormalization($player)) {
            throw new InvalidArgumentException('Not supported');
        }

        /** @var Player $player */
        $user = $player->getUser();
        $data = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'color' => $user->getColor(),
        ];

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Player;
    }
}