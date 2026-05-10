<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260509121415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD total DOUBLE PRECISION DEFAULT NULL, CHANGE statut statut VARCHAR(50) NOT NULL, CHANGE utilisateur_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DFB88E14F ON commande (utilisateur_id)');
        $this->addSql('ALTER TABLE produit CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(100) DEFAULT NULL, ADD prenom VARCHAR(100) DEFAULT NULL, ADD is_enabled TINYINT NOT NULL, ADD confirmation_token VARCHAR(255) DEFAULT NULL, ADD reset_token VARCHAR(255) DEFAULT NULL, ADD reset_expires_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_identifier_email TO UNIQ_8D93D649E7927C74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFB88E14F');
        $this->addSql('DROP INDEX IDX_6EEAA67DFB88E14F ON commande');
        $this->addSql('ALTER TABLE commande DROP total, CHANGE statut statut VARCHAR(255) NOT NULL, CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` DROP nom, DROP prenom, DROP is_enabled, DROP confirmation_token, DROP reset_token, DROP reset_expires_at');
        $this->addSql('ALTER TABLE `user` RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_IDENTIFIER_EMAIL');
    }
}
