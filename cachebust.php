<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 08.11.2017
 * Time: 10:36
 */

const HASHLENGTH = 6;
const PUBLICFOLDER = __DIR__.'/web';

$publicfiles = [
    '/js/KaroApp.dev.js',
    '/js/KaroApp.src.js',
    '/js/KaroApp.min.js',
    '/js/KaroApp.dev.js',
    '/js/Karopapier.src.js',
    '/css/app.css',
    '/css/previous.css',
    '/css/theme.css',
];

$manifest = [];

foreach ($publicfiles as $file) {
    $fileparts = explode('.', $file);
    $ext = array_pop($fileparts);
    $basefilepath = PUBLICFOLDER.implode('.', $fileparts);
    $fullfile = $basefilepath.'.'.$ext;
    $parts = explode('/', $file);
    $filename = array_pop($parts);
    $filenameparts = explode('.', $filename);
    $ext = array_pop($filenameparts);
    $basefilename = implode('.', $filenameparts);

    $basedir = PUBLICFOLDER.implode('/', $parts);
    $regex = '/'.preg_quote($basefilename).'.[a-z\d]{'.HASHLENGTH.'}.'.$ext.'/';

    if ($handle = opendir($basedir)) {
        while (false !== ($entry = readdir($handle))) {
            if (is_dir($entry)) {
                continue;
            }

            if (preg_match($regex, $entry)) {
                echo "Delete old hash file: ".$entry."\n";
                unlink($basedir.'/'.$entry);
            }
        }
        closedir($handle);
    }

    if (file_exists($fullfile)) {
        $hash = substr(md5(file_get_contents($fullfile)), 0, HASHLENGTH);
        $hashpath = $basefilepath.'.'.$hash.'.'.$ext;
        copy($fullfile, $hashpath);


        $manifestpath = implode('.', $fileparts).'.'.$ext;
        $manifesthashpath = implode('.', $fileparts).'.'.$hash.'.'.$ext;

        $manifest[$manifestpath] = $manifesthashpath;
    }

}

file_put_contents(
    PUBLICFOLDER.'/cachebust.json',
    json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

