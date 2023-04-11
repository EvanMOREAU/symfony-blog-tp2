<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321161218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, speaker_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, capacity INT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', content VARCHAR(255) DEFAULT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_404021BFD04A0F27 (speaker_id), INDEX IDX_404021BFB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFD04A0F27 FOREIGN KEY (speaker_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFB03A8386 FOREIGN KEY (created_by_id) REFERENCES tbl_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFD04A0F27');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFB03A8386');
        $this->addSql('DROP TABLE formation');
    }
}
