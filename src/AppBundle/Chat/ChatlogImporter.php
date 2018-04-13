<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 28.01.2017
 * Time: 23:39
 */

namespace AppBundle\Chat;


use AppBundle\Services\LegacyChatlineConverter;
use AppBundle\Services\Smilifier;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class ChatlogImporter
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ChatService
     */
    private $chatService;

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


    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $userRepo;


    public function __construct(
        ObjectManager $em,
        ChatService $chatService,
        Smilifier $smilifier,
        LegacyChatlineConverter $chatlineConverter,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->chatService = $chatService;
        $this->smilifier = $smilifier;
        $this->chatlineConverter = $chatlineConverter;
        $this->logger = $logger;

        $this->userRepo = $em->getRepository("AppBundle:User");
    }

    public function import($path)
    {
        ini_set('pcre.backtrack_limit', '2048576');

        //clear chat table
        $this->em->createQueryBuilder("AppBundle:ChatMessage")
            ->delete('AppBundle:ChatMessage')
            ->getQuery()
            ->execute();

        //reset auto increment
        $conn = $this->em->getConnection();
        $sql = "ALTER TABLE karo_chat AUTO_INCREMENT = 0;";
        $res = $conn->query($sql);

        //read chat file
        $fp = fopen($path, 'rb');
        if (!$fp) {
            throw new Exception("Chatlog not found");
        }

        /*
        $batchSize = 1;
        $skip = 351572;
        $break = 400000;
        */

        $batchSize = 100;
        $skip = 1960;
        #$break = $skip + 4;
        #$break = 10000;


        die("Note an selbst: Jetzt kommt demaiziere, irgendwo nach 350000");

        $confirmedToWorkFor = array(
            21020,    #Madeleines erste krasse Zeile mit Sonderzeichen
            351300,
        );
        $skip = $confirmedToWorkFor[count($confirmedToWorkFor) - 1];
        $break = $skip + 10000;
        $batchSize = 100;
        $break = 400000;


        $lineId = 0;
        while (!feof($fp) && $break > 0) {
            $lineId++;
            $break--;
            $line = fgets($fp);

            if (in_array($lineId, $confirmedToWorkFor)) {
                continue;
            }

            #echo "Line wie aus file: " . $line;

            if ($lineId < $skip) {
                continue;
            }

            if ($lineId % $batchSize == 0) {
                $this->em->flush();
                $this->em->clear();
                $this->logger->info("Flushing ".$lineId);
            }
            #echo "ID: $lineId\n";

            $raw = "";
            $parsed = $this->chatlineConverter->parseLegacyChatline($line);
            $login = "";
            if (isset($parsed["login"])) {
                $login = $parsed["login"];
            }

            $user = null;
            if ($login) {
                $user = $this->userRepo->getUserForLogin($login);
            }

            if (isset($parsed["text"])) {
                $text = $parsed["text"];
            } else {
                if ($lineId = 230972) { #230972 <I><BIG><B>BOTRIX</B> WIRD VOM BLITZ GETROFFEN</BIG></I>
                    $text = $line;
                    $raw = $line;
                } else {
                    echo "WTF??????????? <$lineId>\n\n";
                    var_dump($line);
                    var_dump($parsed);
                    die();
                }
            }

            /**
             * Solange utf8 decode bis kein multibyte mehr drin ist und dann das davor nehmen,
             */

            $prev = $text;
            while ($this->detectUTF8($text)) {
                #echo "New prev: $text";
                $prev = $text;
                $text = utf8_decode($text);
            }
            $text = $prev;

            /**
             * Bleibt die Frage: Ist es jetzt WIRKLICH valid?
             * Wenn nicht, solang wieder encode bis es valid ist. Scheint auch mit Madeleine 21020 zu klappen
             */

            $valid = mb_check_encoding($text, "UTF-8");
            $safety = 10;
            if (!$valid) {
                while (!$valid && $safety > 0) {
                    $text = utf8_encode($text);
                    $valid = mb_check_encoding($text, "UTF-8");
                    $safety--;
                }

                if ($safety <= 0) {
                    echo "Safty: $safety, Valid: ".($valid ? "Y" : "N")."\n";
                    die();
                }
            }

            if (!$raw) {
                $raw = $this->smilifier->guessRaw($text);
            }

            if (isset($parsed["time"])) {
                $time = $parsed["time"];
            } else {
                $time = "00:00";
            }

            $chatMessage = $this->chatService->add($user, $raw, $lineId, $line, $time);
            $chatMessage->setBeforeTs(null);
            $chatMessage->setAfterTs(null);
            $chatMessage->setTs(null);

        }
        $this->em->flush();
        $this->em->clear();

        $this->logger->info("Done");
    }

    private function detectUTF8($string)
    {
        return preg_match(
            '%(?:
            [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
            |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
            |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
            |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
            |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
            |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
            |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
            )+%xs',
            $string
        );
    }
}
