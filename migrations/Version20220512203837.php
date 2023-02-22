<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220512203837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_like (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, blog_id INT DEFAULT NULL, INDEX IDX_4CB3CC23A76ED395 (user_id), INDEX IDX_4CB3CC23DAE07E97 (blog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_like ADD CONSTRAINT FK_4CB3CC23A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blog_like ADD CONSTRAINT FK_4CB3CC23DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE blog ADD view VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE blog_review ADD idblog_id INT DEFAULT NULL, ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE blog_review ADD CONSTRAINT FK_EC0C24075E42CF9 FOREIGN KEY (idblog_id) REFERENCES blog (id)');
        $this->addSql('CREATE INDEX IDX_EC0C24075E42CF9 ON blog_review (idblog_id)');
        $this->addSql('ALTER TABLE planning CHANGE date date DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE blog_like');
        $this->addSql('ALTER TABLE blog DROP view');
        $this->addSql('ALTER TABLE blog_review DROP FOREIGN KEY FK_EC0C24075E42CF9');
        $this->addSql('DROP INDEX IDX_EC0C24075E42CF9 ON blog_review');
        $this->addSql('ALTER TABLE blog_review DROP idblog_id, DROP name');
        $this->addSql('ALTER TABLE planning CHANGE date date DATE DEFAULT NULL');
    }
}
