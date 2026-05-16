<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260513175141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD adresse_livraison VARCHAR(500) DEFAULT NULL, ADD telephone VARCHAR(20) DEFAULT NULL, ADD gouvernorat VARCHAR(100) DEFAULT NULL, ADD code_postal VARCHAR(10) DEFAULT NULL, ADD mode_paiement VARCHAR(50) DEFAULT NULL, ADD total DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP adresse_livraison, DROP telephone, DROP gouvernorat, DROP code_postal, DROP mode_paiement, DROP total');
    }
}
