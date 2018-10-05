<?php

namespace AppBundle\Command;

use AppBundle\Exception\InvalidUsernameException;
use AppBundle\ValueObject\Username;
use Doctrine\DBAL\Connection;
use PDO;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct($name = null, Connection $connection)
    {
        parent::__construct($name);
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('karopapier:test')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sql = "SELECT * FROM karo_user";
        $stmt = $this->connection->executeQuery($sql);
        $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($userData as $data) {
            $login = $data['Login'];
            if (!$login) {
                continue;
            }

            try {
                $username = new Username($login);
                // $output->writeln('Gut '.$login);
            } catch (InvalidUsernameException $ex) {
                $output->writeln('Nix gut '.$login.': '.$data["reallastvisit"]);
            }
        }
    }
}
