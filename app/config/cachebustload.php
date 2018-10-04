<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 15.03.2018
 * Time: 15:52
 */

$manifestpath = __DIR__.'/../../web/cachebust.json';
$manifest = json_decode(file_get_contents($manifestpath), true);

$container->setParameter('app.cachebust', $manifest);
