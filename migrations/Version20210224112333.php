<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224112333 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD user_agence_depot_id INT NOT NULL, ADD user_agence_retrait_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1B644B8CC FOREIGN KEY (user_agence_depot_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D142082B8E FOREIGN KEY (user_agence_retrait_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_723705D1B644B8CC ON transaction (user_agence_depot_id)');
        $this->addSql('CREATE INDEX IDX_723705D142082B8E ON transaction (user_agence_retrait_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1B644B8CC');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D142082B8E');
        $this->addSql('DROP INDEX IDX_723705D1B644B8CC ON transaction');
        $this->addSql('DROP INDEX IDX_723705D142082B8E ON transaction');
        $this->addSql('ALTER TABLE transaction DROP user_agence_depot_id, DROP user_agence_retrait_id');
    }
}
