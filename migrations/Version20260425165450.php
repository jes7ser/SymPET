<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260425165450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY `FK_3170B74B1A65C546`');
        $this->addSql('DROP INDEX IDX_3170B74B1A65C546 ON ligne_commande');
        $this->addSql('ALTER TABLE ligne_commande DROP no_id');
        $this->addSql('ALTER TABLE produit ADD description LONGTEXT DEFAULT NULL, ADD prix DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_commande ADD no_id INT NOT NULL');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT `FK_3170B74B1A65C546` FOREIGN KEY (no_id) REFERENCES commande (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3170B74B1A65C546 ON ligne_commande (no_id)');
        $this->addSql('ALTER TABLE produit DROP description, DROP prix');
    }
}
