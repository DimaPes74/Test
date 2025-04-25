<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304130842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }


    public function up(Schema $schema): void
    {
        $this->addSql('
        CREATE TABLE category (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL UNIQUE,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
    ');

        $this->addSql('
        CREATE TABLE book (
            id INT AUTO_INCREMENT NOT NULL,
            category_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            published_at DATE NOT NULL,
            isbn VARCHAR(13) NOT NULL,
            INDEX IDX_BOOK_CATEGORY_ID (category_id),
            PRIMARY KEY(id),
            CONSTRAINT FK_BOOK_CATEGORY_ID FOREIGN KEY (category_id) REFERENCES category (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
    ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE category');
    }
}
