<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 28.01.2017
 * Time: 23:39
 */

namespace AppBundle\Services;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class ChatlogImporter
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Smilifier
     */
    private $smilifier;

    /**
     * @var LegacyChatlineConverter
     */
    private $chatlineConverter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Registry $registry, Smilifier $smilifier, LegacyChatlineConverter $chatlineConverter, LoggerInterface $logger)
    {
        $this->registry = $registry;
        $this->smilifier = $smilifier;
        $this->chatlineConverter = $chatlineConverter;
        $this->logger = $logger;
    }

    public function import($path)
    {
        $fp = fopen($path, "r");
        if (!$fp) {
            throw new Exception("Chatlog not found");
        }

        $break = 10;
        while (!feof($fp) && $break > 0) {
            $line = fgets($fp);
            echo $line;

            $parsed = $this->chatlineConverter->parseLegacyChatline($line);
            var_dump($parsed);
            die();
            $raw = $this->smilifier->guessRaw($line);

            echo $raw;
            echo "\n";

            $break--;
        }

    }

}