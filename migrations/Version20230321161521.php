<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321161521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_formation (id INT AUTO_INCREMENT NOT NULL, speaker_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, capacity INT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_5C84E2CD04A0F27 (speaker_id), INDEX IDX_5C84E2CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, text LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tbl_formation ADD CONSTRAINT FK_5C84E2CD04A0F27 FOREIGN KEY (speaker_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE tbl_formation ADD CONSTRAINT FK_5C84E2CB03A8386 FOREIGN KEY (created_by_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFB03A8386');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFD04A0F27');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE page');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, speaker_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, capacity INT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', content VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_404021BFD04A0F27 (speaker_id), INDEX IDX_404021BFB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, text LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFB03A8386 FOREIGN KEY (created_by_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFD04A0F27 FOREIGN KEY (speaker_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE tbl_formation DROP FOREIGN KEY FK_5C84E2CD04A0F27');
        $this->addSql('ALTER TABLE tbl_formation DROP FOREIGN KEY FK_5C84E2CB03A8386');
        $this->addSql('DROP TABLE tbl_formation');
        $this->addSql('DROP TABLE tbl_page');
    }
}
