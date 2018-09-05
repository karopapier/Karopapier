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
        $cps = $player->getCheckpoints();

        $user = $player->getUser();
        $data = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'color' => $user->getColor(),
            'moved' => $player->hasMoved(),
            'position' => $player->getFinished(),
            "checkedCps" => $cps,
//            "status" => "ok", @TODO Add
//            "missingCps" => [], @TODO add
        ];

        if ($withMoves) {
            $movesData = [];
            $crashCount = 0;
            foreach ($player->getMoves() as $move) {
                $movesData[] = $this->normalizer->normalize($move);
                if ($move->isCrash()) {
                    $crashCount++;
                }
            }
            $data['moveCount'] = count($movesData);
            $data['crashCount'] = $crashCount;
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