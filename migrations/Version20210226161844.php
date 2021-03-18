<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210226161844 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE numero_cni numero_cni VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE transaction CHANGE frais frais INT DEFAULT NULL, CHANGE part_agence_depot part_agence_depot INT DEFAULT NULL, CHANGE part_agence_retrait part_agence_retrait INT DEFAULT NULL, CHANGE part_etat part_etat INT DEFAULT NULL, CHANGE part_systeme part_systeme INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE numero_cni numero_cni INT NOT NULL');
        $this->addSql('ALTER TABLE transaction CHANGE frais frais INT NOT NULL, CHANGE part_agence_depot part_agence_depot INT NOT NULL, CHANGE part_agence_retrait part_agence_retrait INT NOT NULL, CHANGE part_etat part_etat INT NOT NULL, CHANGE part_systeme part_systeme INT NOT NULL');
    }
}
