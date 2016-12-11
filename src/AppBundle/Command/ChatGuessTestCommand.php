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
                8462, //dis(s)approve problem
                10842, //dis(s)approve problem
                10952, //-:Blöd
                16885, //troest nicht ersetzt
                16904, //troest nicht ersetzt
                17334, //troest nicht ersetzt
                17421, //troest nicht ersetzt
                19148, //dis(s)approve problem
                35808, //:fluch: nicht ersetzt
                35812, //:fluch: nicht ersetzt
                35819, //:fluch: nicht ersetzt
                35823, //:fluch: nicht ersetzt
                39268, //:muede: nicht ersetzt
                39270, //:muede: nicht ersetzt
                39289, //:muede: nicht ersetzt
                39291, //:muede: nicht ersetzt
                39295, //:muede: nicht ersetzt
                55619, //nbsp
                69443, //:happy: nicht ersetzt
                79548, //:music: nicht ersetzt
                83144, //:wavey: nicht ersetzt
                83145, //:wavey: nicht ersetzt
                83146, //:wavey: nicht ersetzt
                83146, //:confused: nicht ersetzt
                83147, //:confused: nicht ersetzt
                83148, //:smile: nicht ersetzt
                85469, //nbsp
                85476, //nbsp
                85478, //nbsp
                85489, //nbsp
                85491, //nbsp
                85493, //nbsp
                85565, //nbsp
                85567, //nbsp
                85569, //nbsp
                85577, //nbsp
                85579, //nbsp
                85590, //nbsp
                85592, //nbsp
                85602, //nbsp
                86345, //regexp
                86592, //nbsp
                86594, //nbsp
                87700, //:zzzsleep: nicht ersetzt
                87701, //nbsp
                88018, //nbsp
                88417, //nbsp
                92097, //nbsp
                92098, //nbsp
                92100, //nbsp
                92102, //nbsp
                92106, //nbsp
                92109, //nbsp
                92117, //nbsp
                92120, //nbsp
                92122, //nbsp
                92124, //nbsp
                92129, //nbsp
                92134, //nbsp
                92139, //nbsp
                92143, //nbsp
                92155, //nbsp
                92158, //nbsp
                92160, //nbsp
                92162, //nbsp
                94042, //-:B nicht ersetzt
                98110, //nbsp
                132451, //:music: nicht ersetzt
                133657, //:music: nicht ersetzt
                134048, //:music: nicht ersetzt
                136180, //:music: nicht ersetzt
                147467, //:music: nicht ersetzt
                170909, //Didi hat mit font size gepfuscht
                177750, //:music: nicht ersetzt
                182194, //:rofl: nicht ersetzt
                182198, //:rofl: nicht ersetzt
                182205, //:wavey: nicht ersetzt
                182207, //:wavey: nicht ersetzt
                230972, //Didi hat im Chat gepfuscht - <I><BIG><B>BOTRIX</B> WIRD VOM BLITZ GETROFFEN</BIG></I>
                238236,  //multiple pics
                260507, //:glover: nicht ersetzt
                260508, //:glover: nicht ersetzt
                260514, //:glover: nicht ersetzt
                260583, //:glover: nicht ersetzt
                266621, //<B>
                268329, //<B>
                306876, //nbsp
                313946, //nbsp
                344654, //JS code
                344760, //fixed encoding fuckup
                346755, //fixed encoding fuckup
                346759, //fixed encoding fuckup
                350535, //fixed encoding fuckup
                350536, //fixed encoding fuckup
                351575, //fixed encoding fuckup
                358874, //fixed encoding fuckup
                364595, //fixed encoding fuckup
                364606, //fixed encoding fuckup
                375218, //encoding fuckup
                375219, //encoding fuckup
                375220, //encoding fuckup
                376711, //encoding fuckup
                376712, //encoding fuckup
                379764, //encoding fuckup
        );

        $fs = new Filesystem();
        $file = new \SplFileObject('/var/wwwroot/karo/karopapier.de/httpdocs/chat.logfile');
        $lc = 0;
        $smileyType = "legacy";
        $picType = "legacy";
        $linkType = "quoteless";
        $escapeAmp = false;
        $escapedAmp = false;
        $preventBR = false;
        $replaceSpaces = false;
        $maxAccept = $accept[count($accept) - 1];

        while (!$file->eof()) {
            $lc++;
            $line = $file->fgets();

            //switches in types
            if ($lc == 40347) {
                $replaceSpaces = true;
            }

            if ($lc == 92270) {
                $smileyType = "quotesnoalt";
                $picType = "quotesnoslash";
                $linkType = "quotes";
            }
            if ($lc == 182211) {
                $smileyType = "quotes";
            }

            if ($lc == 212269) {
                $picType = "quotes";
            }

            //skip until last accept
            if ($lc < $maxAccept) continue;

            if (preg_match('/--( +)(\d\d\d\d-\d\d-\d\d)( +)--/', $line)) {
                continue;
            }

            preg_match('/<B>(.*?)<\/B> \((.*?)\): (.*)<BR>/', $line, $matches);
            if (count($matches) < 3) {
                if (in_array($lc, $accept)) {
                    continue;
                } else {
                    echo "FEHLERLINE $lc: $line";
                    var_dump($matches);
                    die();
                }
            }

            $text = $matches[3];
            if (!$text) die("FEHLT");

            $text = str_replace("\n", '', $text);

            // adjust smiley type according to age/line counter


            $raw = $smilifier->guessRaw($text, $escapedAmp, $preventBR);

            $smil = trim(strtolower($smilifier->smilify($raw, $smileyType, $picType, $linkType, $escapeAmp, $replaceSpaces)));
            $smil2 = trim(strtolower($smilifier->smilify($raw, "quotesnoalt", "quotesnoslash", $linkType, $escapeAmp, true))); //alte chatenter
            $text = trim(strtolower($text));

            //zum vergleichen alle nbsps wieder nach " "
            $testtext = str_replace('&nbsp;', ' ', $text);
            $smil = str_replace('&nbsp;', ' ', $smil);
            $smil2 = str_replace('&nbsp;', ' ', $smil2);
            if (($testtext != $smil) && ($testtext != $smil2)) {
                if (!in_array($lc, $accept)) {
                    echo "Mismatch in $lc: [$line]\n";
                    echo "Line: |" . $text . "|\n";
                    echo "Raw:  |" . $raw . "|\n";
                    echo "Smil: |" . $smil . "| (len: " . strlen($smil) . ")\n";
                    return;
                }
            }
            echo "$lc\n";
        }
    }
}
