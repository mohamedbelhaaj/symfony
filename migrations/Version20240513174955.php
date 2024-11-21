<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240513174955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire CHANGE date date DATE NOT NULL');
        $this->addSql('ALTER TABLE publication CHANGE date date DATE NOT NULL, CHANGE image image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('ALTER TABLE reclamation DROP tel, CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX fk_ce606404a76ed395 ON reclamation');
        $this->addSql('CREATE INDEX IDX_CE606404A76ED395 ON reclamation (user_id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire CHANGE date date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE publication CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('ALTER TABLE reclamation ADD tel INT NOT NULL, CHANGE email email VARCHAR(200) NOT NULL');
        $this->addSql('DROP INDEX idx_ce606404a76ed395 ON reclamation');
        $this->addSql('CREATE INDEX FK_CE606404A76ED395 ON reclamation (user_id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
    }
}
