<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 15.03.2018
 * Time: 15:52
 */

include(__DIR__.'/private_fallback_config.php');
include(__DIR__.'/../../private/config.php');

$configuration = [
    'realtime_host' => [
        'prefix' => (string) $REALTIME_PREFIX_INT,
        'suffix' => $REALTIME_SUFFIX_INT,
        'num_hosts' => $REALTIME_NUM_HOSTS,
        'host_offset' => $REALTIME_NUM_HOST_OFFSET,
    ],
    'realtime_host_ext' => [
        'prefix' => $REALTIME_PREFIX,
        'suffix' => $REALTIME_SUFFIX,
        'num_hosts' => $REALTIME_NUM_HOSTS,
        'host_offset' => $REALTIME_NUM_HOST_OFFSET,
    ],
    'online_host' => $ONLINE_HOST,
    'client_token_prefix' => $CLIENT_TOKEN_PREFIX,
    'client_token_suffix' => $CLIENT_TOKEN_SUFFIX,
    'sys_token_prefix' => $SYS_TOKEN_PREFIX,
    'sys_token_suffix' => $SYS_TOKEN_SUFFIX,
    'dingdong_api_host' => $DINGDONG_API_HOST,
    'passion_api_host_int' => $PASSION_API_HOST_INT,
    'wwwstatic' => $WWWSTATIC,
    'wwwupload' => $WWWIMG_UPL,
    'object_store' => [
        'read_enabled' => true,
        'write_enabled' => $OBJECTSTORE_WRITE_ENABLED,
        'conf' => $OBJECTSTORE_CONF,
        'bucket' => $OBJECTSTORE_BUCKET,
        'prefix' => $OBJECTSTORE_PREFIX,
    ],
    'affilinet' => $AFFILINET,
    'messaging' => [
        'contactlimit_basic' => $CONTACTLIMIT_BASIC,
        'contactlimit_club' => $CONTACTLIMIT_CLUB,
        'message_limit' => $MESSAGELIMIT,
    ],
    'cron' => [
        'interval' => 3,
        'env' => 'prod',
    ],
    'maint' => [
        'lockdir' => __DIR__.'/../../var',
    ],
    'mail' => [
        // Anzahl Kontakte basic
        'contactlimit_basic' => $CONTACTLIMIT_BASIC,
        // Anzahl Kontakte mit Club
        'contactlimit_club' => $CONTACTLIMIT_CLUB,
        // Maximale Anzahl Kontakte, bevor unscharfe entfernt werden
        'contactlimit_max' => 50,
        // Max Nachrichtenlaenge
        'message_limit' => $MESSAGELIMIT,
        // Benachrichtigung fuer ungelesene nach XX Sekunden
        'notification_delay' => 900,
        // wie oft Benachrichtigung Ungelesene, reset beim Laden der Contacts
        'notification_interval' => 86400,
        // wie lange wir warten, bis wir eine conversation kuerzen
        'processing_delay' => 300,
        // Overflow, wieviele Mails in Archiv-Email kommen, abgeschnitten wird also bei 40+40 oder 40+80
        'dialog_overflow' => 40,
        // verbleibende Mails im Dialog (Basic)
        'dialog_limit_basic' => 40,
        // verbleibende Mails im Dialog (wenn einer im Club, gilt dann fuer beide)
        'dialog_limit_club' => 80,
    ],
    'email' => [
        'image_basepath' => 'https://www.lablue.de/images/email',
    ],
    'banking' => [
        'csv_path' => '/data/www/beta.lablue.de/private/bank/import.csv',
    ],
];
$container->setParameter('app.configuration', $configuration);
