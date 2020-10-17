<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201015075944 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        // Etapes pour ajouter une colonne en not null dans une table avec des lignes existantes

        //1. Accepter d'ajouter la colonne en default null
        $this->addSql('ALTER TABLE user ADD pseudo VARCHAR(50) DEFAULT NULL');
        //2. Définir une valeur pour les lignes existante
        $this->addSql('UPDATE user SET pseudo = CONCAT("user_", id)');
        //3. Repasser la colonne en NOT NULL
        $this->addSql('ALTER TABLE user MODIFY pseudo VARCHAR(50) NOT NULL');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64986CC499D ON user (pseudo)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D64986CC499D ON user');
        $this->addSql('ALTER TABLE user DROP pseudo');
    }
}
