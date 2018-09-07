<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 25.10.2017
 * Time: 12:42
 */

namespace AppBundle\Serializer;

use AppBundle\Entity\Player;
use AppBundle\Model\Motion;
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
        $cps = $player->getCheckpointsArray();

        $user = $player->getUser();
        $data = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'color' => $user->getColor(),
            'status' => $player->getStatus(),
            'moved' => $player->hasMoved(),
            'rank' => $player->getFinished(),
            'checkedCps' => $cps,
        ];

        if ($withMoves) {
            $moves = $player->getMovesArray();
            $moveCount = count($moves);
            $data['moveCount'] = $moveCount;
            $data['crashCount'] = $player->getCrashCount();
            $data['moves'] = $moves;

            if ($moveCount > 0) {
                $lastmove = end($data['moves']);
                $data['motion'] = $lastmove;
            }

            $possibles = $player->getPossibleMotions();
            if (count($possibles) > 0) {
                $data['possibles'] = [];
                /** @var Motion $possible */
                foreach ($possibles as $possible) {
                    $data['possibles'][] = $possible->asArray();
                }
            }
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