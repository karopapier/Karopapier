<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DateFixerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('karopapier:datefixer');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $connection = $container->get('doctrine.dbal.default_connection');

        $datecolumns = [
            'Birthday',
            'lastvisit',
            'currentvisit',
            'chatvisit',
            'lastmailsent',
            'maillock',
            'Signupdate',
            'reallastvisit',
        ];

        /*
        //Allow null first
        foreach ($datecolumns as $dc) {
            $changes[] = 'CHANGE  `'.$dc.'`  `'.$dc.'` DATETIME NULL';

        }
        $sql = 'ALTER TABLE  `karo_user` '.implode(', ', $changes);
        $connection->executeQuery($sql);
        */

        // Fix 0000 birthdays
        $sql = 'UPDATE `karo_user` SET Birthday = \'1000-01-01\' WHERE `Birthday` = 0';
        $connection->executeQuery($sql);

        $sql = 'UPDATE `karo_user` SET Birthday = \'1000-01-01\' WHERE `Birthday` = "1900-01-01"';
        $connection->executeQuery($sql);

        $sql = 'UPDATE `karo_user` SET Birthday = \'1000-01-01\' WHERE month(`Birthday`) = 0';
        $connection->executeQuery($sql);

        foreach ($datecolumns as $dc) {
            echo "Fix ".$dc."\n";
            $sql = 'UPDATE `karo_user` SET '.$dc.' = \'1000-01-01\' WHERE `'.$dc.'` = 0';
            $connection->executeQuery($sql);
            $sql = 'UPDATE `karo_user` SET '.$dc.' = \'1000-01-01\' WHERE `'.$dc.'` = \'1900-01-01\'';
            $connection->executeQuery($sql);
        }


        $changes = [];
        foreach ($datecolumns as $dc) {
            $changes[] = 'CHANGE  `'.$dc.'`  `'.$dc.'` DATETIME NULL DEFAULT NULL';

        }
        $sql = 'ALTER TABLE  `karo_user` '.implode(', ', $changes);
        $stmt = $connection->prepare($sql);

        $stmt->execute(['field' => 'lastvisit']);

        foreach ($datecolumns as $dc) {
            echo "Null ".$dc."\n";
            $sql = 'UPDATE `karo_user` SET '.$dc.' = NULL WHERE `'.$dc.'` = \'1000-01-01\'';
            $connection->executeQuery($sql);
        }
    }
}
