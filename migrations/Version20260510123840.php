<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260510123840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit ADD image_url LONGTEXT DEFAULT NULL, ADD animal_type VARCHAR(255) DEFAULT NULL, ADD produit_type VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, CHANGE is_promo is_promo TINYINT NOT NULL, CHANGE is_rupture is_rupture TINYINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP image_url, DROP animal_type, DROP produit_type, DROP created_at, CHANGE is_promo is_promo TINYINT DEFAULT 0 NOT NULL, CHANGE is_rupture is_rupture TINYINT DEFAULT 0 NOT NULL');
    }
}
