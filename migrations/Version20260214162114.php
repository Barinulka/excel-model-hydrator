<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260214162114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main ADD COLUMN IF NOT EXISTS created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE main ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('UPDATE main SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL OR updated_at IS NULL');
        $this->addSql('ALTER TABLE main ALTER COLUMN created_at SET NOT NULL');
        $this->addSql('ALTER TABLE main ALTER COLUMN updated_at SET NOT NULL');

        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('UPDATE "user" SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL OR updated_at IS NULL');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN created_at SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN updated_at SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main DROP created_at');
        $this->addSql('ALTER TABLE main DROP updated_at');
        $this->addSql('ALTER TABLE "user" DROP created_at');
        $this->addSql('ALTER TABLE "user" DROP updated_at');
    }
}
