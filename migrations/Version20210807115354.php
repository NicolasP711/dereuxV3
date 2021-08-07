<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210807115354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('ALTER TABLE article CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE article_comment DROP FOREIGN KEY FK_79A616DBF675F31B');
        $this->addSql('ALTER TABLE article_comment CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article_comment ADD CONSTRAINT FK_79A616DBF675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('ALTER TABLE article CHANGE author_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article_comment DROP FOREIGN KEY FK_79A616DBF675F31B');
        $this->addSql('ALTER TABLE article_comment CHANGE author_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE article_comment ADD CONSTRAINT FK_79A616DBF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }
}
