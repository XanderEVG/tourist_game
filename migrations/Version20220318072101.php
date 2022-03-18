<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220318072101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE difficulty (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "markers" (id SERIAL NOT NULL, difficulty_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4189DF30FCFA9DAE ON "markers" (difficulty_id)');
        $this->addSql('CREATE INDEX IDX_4189DF30B03A8386 ON "markers" (created_by_id)');
        $this->addSql('COMMENT ON COLUMN "markers".latitude IS \'Широта\'');
        $this->addSql('COMMENT ON COLUMN "markers".longitude IS \'Долгота\'');
        $this->addSql('COMMENT ON COLUMN "markers".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "markers".photo IS \'Фото локации\'');
        $this->addSql('ALTER TABLE "markers" ADD CONSTRAINT FK_4189DF30FCFA9DAE FOREIGN KEY (difficulty_id) REFERENCES difficulty (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "markers" ADD CONSTRAINT FK_4189DF30B03A8386 FOREIGN KEY (created_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ALTER password DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "markers" DROP CONSTRAINT FK_4189DF30FCFA9DAE');
        $this->addSql('DROP TABLE difficulty');
        $this->addSql('DROP TABLE "markers"');
        $this->addSql('ALTER TABLE "users" ALTER password SET NOT NULL');
    }
}
