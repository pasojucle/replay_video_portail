<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210508153036 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel CHANGE id_raspberry id_device INT DEFAULT NULL');
        $this->addSql('ALTER TABLE program CHANGE id_raspberry id_device INT DEFAULT NULL');
        $this->addSql('ALTER TABLE video ADD id_device INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel CHANGE id_device id_raspberry INT DEFAULT NULL');
        $this->addSql('ALTER TABLE program CHANGE id_device id_raspberry INT DEFAULT NULL');
        $this->addSql('ALTER TABLE video DROP id_device');
    }
}
