<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210918173226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, content VARCHAR(10000) NOT NULL, slug VARCHAR(255) NOT NULL, publication_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_23A0E66989D9B62 (slug), INDEX IDX_23A0E66F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_comment (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, article_id INT NOT NULL, content VARCHAR(2000) NOT NULL, slug VARCHAR(255) NOT NULL, publication_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_79A616DB989D9B62 (slug), INDEX IDX_79A616DBF675F31B (author_id), INDEX IDX_79A616DB7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artwork (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(150) NOT NULL, description VARCHAR(3000) NOT NULL, picture VARCHAR(50) NOT NULL, artist VARCHAR(150) NOT NULL, publication_date DATETIME NOT NULL, slug VARCHAR(255) NOT NULL, year_of_creation INT DEFAULT NULL, UNIQUE INDEX UNIQ_881FC576989D9B62 (slug), INDEX IDX_881FC576F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artwork_comment (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, artwork_id INT NOT NULL, content VARCHAR(2000) NOT NULL, slug VARCHAR(255) NOT NULL, publication_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_6730D037989D9B62 (slug), INDEX IDX_6730D037F675F31B (author_id), INDEX IDX_6730D037DB8FFA4 (artwork_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, subject VARCHAR(100) NOT NULL, message VARCHAR(2000) NOT NULL, slug VARCHAR(255) NOT NULL, date_sent DATETIME NOT NULL, email VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_4C62E638989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, slug VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, registration_date DATETIME NOT NULL, is_verified TINYINT(1) NOT NULL, pseudonym VARCHAR(50) NOT NULL, photo VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649989D9B62 (slug), UNIQUE INDEX UNIQ_8D93D6493654B190 (pseudonym), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE article_comment ADD CONSTRAINT FK_79A616DBF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article_comment ADD CONSTRAINT FK_79A616DB7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE artwork ADD CONSTRAINT FK_881FC576F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE artwork_comment ADD CONSTRAINT FK_6730D037F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE artwork_comment ADD CONSTRAINT FK_6730D037DB8FFA4 FOREIGN KEY (artwork_id) REFERENCES artwork (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_comment DROP FOREIGN KEY FK_79A616DB7294869C');
        $this->addSql('ALTER TABLE artwork_comment DROP FOREIGN KEY FK_6730D037DB8FFA4');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('ALTER TABLE article_comment DROP FOREIGN KEY FK_79A616DBF675F31B');
        $this->addSql('ALTER TABLE artwork DROP FOREIGN KEY FK_881FC576F675F31B');
        $this->addSql('ALTER TABLE artwork_comment DROP FOREIGN KEY FK_6730D037F675F31B');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_comment');
        $this->addSql('DROP TABLE artwork');
        $this->addSql('DROP TABLE artwork_comment');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user');
    }
}
