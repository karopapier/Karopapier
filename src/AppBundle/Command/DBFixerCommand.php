<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DBFixerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('karopapier:fix:db');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $connection = $container->get('doctrine.dbal.default_connection');

        $datetimecolumns = [
            'lastvisit',
            'currentvisit',
            'chatvisit',
            'lastmailsent',
            'maillock',
            'Signupdate',
            'reallastvisit',
        ];

        // Fix 0000 birthdays
        $sql = 'UPDATE `karo_user` SET Birthday = \'1000-01-01\' WHERE `Birthday` = 0';
        $connection->executeQuery($sql);

        $sql = 'UPDATE `karo_user` SET Birthday = \'1000-01-01\' WHERE `Birthday` = "1900-01-01"';
        $connection->executeQuery($sql);

        $sql = 'UPDATE `karo_user` SET Birthday = \'1000-01-01\' WHERE month(`Birthday`) = 0';
        $connection->executeQuery($sql);

        foreach ($datetimecolumns as $dc) {
            echo "Fix ".$dc."\n";
            $sql = 'UPDATE `karo_user` SET '.$dc.' = \'1000-01-01\' WHERE `'.$dc.'` = 0';
            $connection->executeQuery($sql);
            $sql = 'UPDATE `karo_user` SET '.$dc.' = \'1000-01-01\' WHERE `'.$dc.'` = \'1900-01-01\'';
            $connection->executeQuery($sql);
        }

        $dc = 'Birthday';
        $sql = 'ALTER TABLE  `karo_user` CHANGE  `'.$dc.'`  `'.$dc.'` DATE NULL DEFAULT NULL';
        $connection->executeQuery($sql);

        $changes = [];
        foreach ($datetimecolumns as $dc) {
            $changes[] = 'CHANGE  `'.$dc.'`  `'.$dc.'` DATETIME NULL DEFAULT NULL';
        }

        $sql = 'ALTER TABLE  `karo_user` '.implode(', ', $changes);
        $connection->executeQuery($sql);

        foreach ($datetimecolumns as $dc) {
            echo "Null ".$dc."\n";
            $sql = 'UPDATE `karo_user` SET '.$dc.' = NULL WHERE `'.$dc.'` = \'1000-01-01\'';
            $connection->executeQuery($sql);
        }


        $sql = 'ALTER TABLE `karo_user` CHANGE `Invited` `Invited` INT(11) UNSIGNED NULL DEFAULT \'0\';';
        $connection->executeQuery($sql);

        $sql = 'ALTER TABLE `karo_maps` ADD `rows` TINYINT UNSIGNED NOT NULL DEFAULT \'0\' AFTER `Starties`, ADD `cols` TINYINT UNSIGNED NOT NULL DEFAULT \'0\' AFTER `rows`';
        $sql = 'alter table `karo_maps` AUTO_INCREMENT=10000';
    }
}
