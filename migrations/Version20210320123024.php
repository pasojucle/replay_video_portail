<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210320123024 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE channel (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, id_raspberry INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, video_id INT NOT NULL, created_at DATETIME NOT NULL, status INT DEFAULT NULL, route VARCHAR(50) NOT NULL, INDEX IDX_8F3F68C529C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE program (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, id_raspberry INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, program_id INT NOT NULL, channel_id INT NOT NULL, title VARCHAR(255) NOT NULL, broadcast_at DATETIME NOT NULL, url VARCHAR(255) DEFAULT NULL, status INT NOT NULL, INDEX IDX_7CC7DA2C3EB8070A (program_id), INDEX IDX_7CC7DA2C72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C529C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C3EB8070A FOREIGN KEY (program_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');

        $this->addSql("INSERT INTO program (`title`) VALUES ('ARTE Concert'),('Cirque'),('Concert'),('Des racines et des ailes'),('Des trains pas comme les autres'),('Documentaire'),('Laissez-vous guider'),
        ('Les 100 lieux qu\'il faut voir'),('Les coulisses de l\'histoire'),('Passage des arts'),('Secrets d\'Histoire'),('Si les murs pouvaient parler')");
        $this->addSql("INSERT INTO channel (`title`) VALUES ('Arté'),('France 2'),('France 3'),('France 5'),('public sénat')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C72F5A1AA');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C3EB8070A');
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C529C1004E');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE program');
        $this->addSql('DROP TABLE video');
    }
}
