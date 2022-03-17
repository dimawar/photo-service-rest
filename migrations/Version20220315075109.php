<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220315075109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_file ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media_file ADD CONSTRAINT FK_4FD8E9C34584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_4FD8E9C34584665A ON media_file (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_file DROP FOREIGN KEY FK_4FD8E9C34584665A');
        $this->addSql('DROP INDEX IDX_4FD8E9C34584665A ON media_file');
        $this->addSql('ALTER TABLE media_file DROP product_id');
    }
}
