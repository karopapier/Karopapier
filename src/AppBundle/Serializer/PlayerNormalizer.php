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
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class PlayerNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    private $normalizer;

    public function normalize($player, $format = null, array $context = array())
    {
        if (!$this->supportsNormalization($player)) {
            throw new InvalidArgumentException('Not supported');
        }

        $withMoves = false;
        if (array_key_exists('moves', $context)) {
            $withMoves = (bool)$context['moves'];
        }

        /** @var Player $player */
        $user = $player->getUser();
        $data = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'color' => $user->getColor(),
            'moved' => $player->hasMoved(),
            'position' => $player->getFinished(),

//            "position" => 4,
//            "status" => "ok",
//            "moveCount" => 157,
//            "crashCount" => 1,
//            "checkedCps" => [1, 2, 3, 4],
//            "missingCps" => [],
        ];

        if ($withMoves) {
            $movesData = [];
            foreach ($player->getMoves() as $move) {

                $movesData[] = $this->normalizer->normalize($move);
            }
            $data['moves'] = $movesData;

            $lastmove = end($data['moves']);
            $data['position'] = $lastmove;
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Player;
    }

    /**
     * Sets the owning Normalizer object.
     *
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }
}