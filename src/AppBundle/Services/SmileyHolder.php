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

/**
 * Class SmileyHolder
 *
 * Intended to find, cache and provide the list of available smilies in the smiley directory
 * This omits the need to parse the directory on every call
 *
 * @package AppBundle\Services
 */
class SmileyHolder implements CacheClearerInterface, CacheWarmerInterface, SmileyHolderInterface
{
    const CACHE_FILE = "/karopapier/smilies.cache.php";

    /** @var  string $cacheDir */
    private $cacheDir;

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

    public function getSmilies()
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