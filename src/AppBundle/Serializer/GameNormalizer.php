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
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GameNormalizer implements NormalizerInterface, NormalizerAwareInterface
{

    private $normalizer;

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
                $options[$option] = (bool) $context[$option];
            }
        }

        /** @var Map $map */
        $map = $game->getMap();
        $mapCps = $map->getCpArray();

        /** @var Game $game */
        $data = [
            'id' => $game->getId(),
            'name' => $game->getName(),
            'mapId' => $map->getId(),
            'map' => [
                'id' => $map->getId(),
                'name' => $map->getName(),
                'cps' => $mapCps,
            ],
            'cps' => (bool) $game->getCheckpointsEnabled(),
            'zzz' => $game->getZzz(),
            'crashallowed' => $game->getCrashAllowed(),
            'startdirection' => $game->getStartDirection(),
        ];

        $data = array_merge($data, $game->getDetailsArray());

        if ($options['mapcode']) {
            $data['map']['code'] = $map->getCode();
        }

        $playersData = [];
        if ($options['players']) {
            $players = $game->getPlayers();
            /** @var Player $player */
            foreach ($players as $player) {
                $playerData = $this->normalizer->normalize($player, null, $context);

                // add missing cps
                $checked = $playerData['checkedCps'];
                $diff = array_diff($mapCps, $checked);
                sort($diff);
                $playerData['missingCps'] = $diff;

                $playersData[] = $playerData;
            }

            $data['players'] = $playersData;
        }

        // dump($p);

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Game;
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