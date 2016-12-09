<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 09.12.2016
 * Time: 22:08
 */

namespace AppBundle\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class Smilifier implements CacheClearerInterface, CacheWarmerInterface
{
    const CACHE_FILE = "/karopapier/smilies.cache.php";

    /** @var  string $smileyDir */
    private $smileyDir;

    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct($cacheDir, $smileyDir, LoggerInterface $logger)
    {
        $this->cacheDir = $cacheDir;
        $this->smileyDir = $smileyDir;
        $this->logger = $logger;
    }

    public static function smilify($content)
    {
        $content = str_replace('<', '&lt;', $content);
        $content = str_replace('>', '&gt;', $content);
        $content = str_replace('\"', '&quot;', $content);
        $content = str_replace("\\\'", "'", $content);
        $content = str_replace('"', '&quot;', $content);
        $content = str_replace("\n", '<BR>', $content);

        $smileyDir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . "smilies";
        #$enter=chr(13);

        $smile = opendir($smileyDir);
        while ($file = readdir($smile)) {
            if (preg_match("/.*\.gif/", $file)) {
                $parts = explode(".", $file);
                #$content = preg_replace ( ":$parts[0]:", image_tag('smilies/' . $parts[0] . '.gif',array('alt_title'=>$parts[0])), $content);
                $content = str_replace(':' . $parts[0] . ':', '<img src="/images/smilies/' . $parts[0] . '.gif" alt="' . $parts[0] . '" title="' . $parts[0] . '">', $content);
            }
        }
        closedir($smile);


        $textparts = explode(":-", $content);
        if (count($textparts) > 1) {
            $content = "";
            for ($i = 0; $i < count($textparts) - 1; $i++) {
                $value = $textparts[$i];
                $value = $value . ":-";
                #echo "VALUE: $value<BR>";
                $value = str_replace(":-F:", "<b>", $value);
                $value = str_replace(":F-:", "</b>", $value);

                $value = str_replace("-:RED", "<font color=#CC0000>", $value);
                $value = str_replace("RED:-", "</font>", $value);

                $value = str_replace("-:K", "<i>", $value);
                $value = str_replace("K:-", "</i>", $value);

                $value = str_replace("-:F", "<b>", $value);
                $value = str_replace("F:-", "</b>", $value);
                $value = str_replace("-:B", "<b>", $value);
                $value = str_replace("B:-", "</b>", $value);
                $value = str_replace(":-Link:", "</a>", $value);
                $value = preg_replace("/-:Link text=(.*) url=(.*) Link:-/", "<a href=\"\\2\">\\1</a>", $value);    //Monti Link
                $value = preg_replace("/-:Pic src=(.*) Pic:-/", "<img src=\"\\1\" />", $value);        //Monti Pic
                $value = str_replace("  ", "&nbsp;&nbsp;", $value);
                $content = $content . $value;
            }
            $content = $content . array_pop($textparts);
        }

        return $content;
    }

    private function getSmilies()
    {
        $smilies = array();
        if (file_exists($this->cacheDir . self::CACHE_FILE)) {
            $smilies = include($this->cacheDir . self::CACHE_FILE);
        }
        $c = count($smilies);
        if ($c > 0) {
            $this->logger->debug("Get " . $c . " smilies from cache");
            return $smilies;
        }

        $this->logger->debug("Parse Smiley Dir");
        $smilies = array();
        $finder = new Finder();
        $finder->files()->name('*.gif');
        $finder->depth('== 0');
        /** @var \SplFileInfo $file */
        foreach ($finder->in($this->smileyDir) as $file) {
            $smilies[] = strtolower($file->getBasename(".gif"));
        }
        return $smilies;
    }

    public function clear($cacheDir)
    {
        $fs = new Filesystem();
        $fs->remove($cacheDir . self::CACHE_FILE);
    }

    public function warmUp($cacheDir)
    {
        $smilies = $this->getSmilies();
        $fs = new Filesystem();
        $fs->dumpFile($cacheDir . self::CACHE_FILE, '<?php return ' . var_export($smilies, true));
    }

    public function isOptional()
    {
        return true;
    }
}