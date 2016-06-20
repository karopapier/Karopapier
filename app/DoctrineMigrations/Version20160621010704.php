<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160621010704 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE karo_user_map ADD creator_id INT DEFAULT NULL, ADD archived TINYINT(1) DEFAULT \'0\', CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE used used TINYINT(1) DEFAULT \'0\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_63692D2961220EA6 ON karo_user_map (creator_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_63692D2961220EA6 ON karo_user_map');
        $this->addSql('ALTER TABLE karo_user_map DROP creator_id, DROP archived, CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE used used TINYINT(1) DEFAULT NULL');
    }
}
