<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ChatGuessTestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
                ->setName('karopapier:chatguesstest')
                ->setDescription('Run over the original chat log and try to guess the raw entry, smilify and compare results');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $chatService = $container->get("chat_service");
        $smilifier = $container->get("smilifier");
        $accept = array(
                5645, //<BR> mitten drin!!!
                6980, //tabelle
                6996, //tabelle
                7020, //tabelle
                8462, //dis(s)approve problem
                10842, //dis(s)approve problem
                10952, //-:Blöd
            /*
            347, //da wurden & noch nicht escaped geschrieben
            1404, //ein &gt statt &gt;
            1862, //da wurden & noch nicht escaped geschrieben
            2199, //da wurden & noch nicht escaped geschrieben
            2257, //da wurden & noch nicht escaped geschrieben
            2261, //da wurden & noch nicht escaped geschrieben
            2264, //da wurden & noch nicht escaped geschrieben
            2902, //da wurden & noch nicht escaped geschrieben
            2986, //da wurden & noch nicht escaped geschrieben
            3006, //da wurden & noch nicht escaped geschrieben
            3278, //&copy;
            3470, //da wurden & noch nicht escaped geschrieben
            3659, //ein &gt statt &gt;
            5524, //´
            5688, //´
            5934, //da wurden & noch nicht escaped geschrieben
            5936, //da wurden & noch nicht escaped geschrieben
            6052, //unicode quark
            6054, //unicode quark
            6056, //unicode quark
            6057, //unicode quark
            6116, //da wurden & noch nicht escaped geschrieben
            6565, //da wurden & noch nicht escaped geschrieben
            7590, //unicode quark
            7666, //unicode/html quark
            7667, //unicode/html quark
            7668, //unicode/html quark
            7669, //unicode/html quark
            7839, //da wurden & noch nicht escaped geschrieben
            8208, //&copy;
            8209, //html codes
            8258, //&copy;
            9443, //da wurden & noch nicht escaped geschrieben
            9498, //da wurden & noch nicht escaped geschrieben
            9961, //da wurden & noch nicht escaped geschrieben
            11201, //da wurden & noch nicht escaped geschrieben
            11390, //<BR> mitten drin!!!
            11406, //da wurden & noch nicht escaped geschrieben
            11722, //&copy;
            11726, //&copy;
        /*
            11727, //da wurden & noch nicht escaped geschrieben
            11772, //da wurden & noch nicht escaped geschrieben
            11825, //da wurden & noch nicht escaped geschrieben
            11828, //da wurden & noch nicht escaped geschrieben
            11834, //da wurden & noch nicht escaped geschrieben
            11856, //da wurden & noch nicht escaped geschrieben
            11861, //da wurden & noch nicht escaped geschrieben
            11865, //da wurden & noch nicht escaped geschrieben
            12003, //da wurden & noch nicht escaped geschrieben
            12176, //da wurden & noch nicht escaped geschrieben
            12286, //da wurden & noch nicht escaped geschrieben
            12288, //da wurden & noch nicht escaped geschrieben
            13019, //da wurden & noch nicht escaped geschrieben
            13021, //da wurden & noch nicht escaped geschrieben
            13159, //da wurden & noch nicht escaped geschrieben
            13163, //da wurden & noch nicht escaped geschrieben
            13166, //da wurden & noch nicht escaped geschrieben
            13369, //da wurden & noch nicht escaped geschrieben
            13377, //html codes
            13379, //html codes
            13382, //unicode quark
            13384, //unicode quark
            13395, //unicode quark
            13401, //unicode quark
            13838, //&copy;
            14244, //da wurden & noch nicht escaped geschrieben
            14248, //da wurden & noch nicht escaped geschrieben
            14449, //&szlig;
            14616, //&szlig;
            14840, //&szlig;
            16215, //<BR> mitten drin!!!
            16885, //troest nicht ersetzt
            16904, //troest nicht ersetzt
            17334, //troest nicht ersetzt
            17421, //troest nicht ersetzt
            17640, //<BR> mitten drin!!!
            19148, //dis(s)approve problem
            20175, //<BR> mitten drin!!!
            20180, //amp
            20249, //unicode quark
            20252, //unicode quark
            21175, //&copy;
            24591, //nbsps
            26819, //<BR> mitten drin!!!
            30813, //da wurden & noch nicht escaped geschrieben
            35808, //:fluch: nicht ersetzt
            35812, //:fluch: nicht ersetzt
            35819, //:fluch: nicht ersetzt
            35823, //:fluch: nicht ersetzt
            36928, //<BR> mitten drin!!!
            39268, //:muede: nicht ersetzt
            39270, //:muede: nicht ersetzt
            39289, //:muede: nicht ersetzt
            39291, //:muede: nicht ersetzt
            100000, //--WEITER REIN
            101671, //<BR> mitten drin!!!
            101673, //<BR> mitten drin!!!
            101691, //<BR> mitten drin!!!
            106699, //nbsp
            106700, //nbsp
            106701, //nbsp
            106702, //nbsp
            106703, //nbsp
            106704, //nbsp
            107066, //nbsp
            110610, //<BR> mitten drin!!!
            149500, //--WEITER REIN

        */


        );

        $fs = new Filesystem();
        $file = new \SplFileObject('/var/wwwroot/karo/karopapier.de/httpdocs/chat.logfile');
        $lc = 0;
        $smileyType = "legacy";
        $picType = "legacy";
        $linkType = "quoteless";
        $escapeAmp = false;
        $escapedAmp = false;
        $maxAccept = $accept[count($accept) - 1];

        while (!$file->eof()) {
            $lc++;
            $line = $file->fgets();

            //switches in types
            if ($lc == 100000) {
                $smileyType = "quotesnoalt";
                $picType = "quotesnoslash";
                $linkType = "quotes";
            }
            if ($lc == 200000) {
                $escapeAmp = true;
            }

            //skip until last accept
            if ($lc < $maxAccept) continue;

            if (preg_match('/--( +)(\d\d\d\d-\d\d-\d\d)( +)--/', $line)) {
                continue;
            }

            preg_match('/<B>(.*?)<\/B> \((.*?)\): (.*)<BR>/', $line, $matches);
            if (count($matches) < 3) {
                echo "FEHLERLINE: $line";
                var_dump($matches);
                die();
            }
            $text = $matches[3];
            if (!$text) die("FEHLT");

            $text = str_replace("\n", '', $text);

            // adjust smiley type according to age/line counter


            $raw = $smilifier->guessRaw($text, $escapedAmp);
            $smil = trim(strtolower($smilifier->smilify($raw, $smileyType, $picType, $linkType, $escapeAmp)));
            $text = trim(strtolower($text));

            if ($text != $smil) {
                if (!in_array($lc, $accept)) {
                    echo "Mismatch in $lc: [$line]\n";
                    echo "Line: |" . $text . "\n";
                    echo "Raw:  |" . $raw . "\n";
                    echo "Smil: |" . $smil . "\n";
                    return;
                }
            }
        }
    }
}
