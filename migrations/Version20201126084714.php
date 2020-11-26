<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201126084714 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation CHANGE categorie_id categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session CHANGE formation_id formation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stagiaire_session DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE stagiaire_session ADD PRIMARY KEY (stagiaire_id, session_id)');
        $this->addSql('ALTER TABLE stagiaire_session RENAME INDEX fk_d32d02d4613fecdf TO IDX_D32D02D4613FECDF');
        $this->addSql('ALTER TABLE user ADD photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation CHANGE categorie_id categorie_id INT NOT NULL');
        $this->addSql('ALTER TABLE session CHANGE formation_id formation_id INT NOT NULL');
        $this->addSql('ALTER TABLE stagiaire_session DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE stagiaire_session ADD PRIMARY KEY (stagiaire_id)');
        $this->addSql('ALTER TABLE stagiaire_session RENAME INDEX idx_d32d02d4613fecdf TO FK_D32D02D4613FECDF');
        $this->addSql('ALTER TABLE user DROP photo');
    }
}
