<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200424210938 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE advert (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, fuel_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, year_started_at DATETIME NOT NULL, km DOUBLE PRECISION NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_54F1F40BC4FFF555 (garage_id), INDEX IDX_54F1F40B97C79677 (fuel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fuel (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE garage (id INT AUTO_INCREMENT NOT NULL, professional_id INT NOT NULL, name VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, INDEX IDX_9F26610BDB77003 (professional_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE advert ADD CONSTRAINT FK_54F1F40BC4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id)');
        $this->addSql('ALTER TABLE advert ADD CONSTRAINT FK_54F1F40B97C79677 FOREIGN KEY (fuel_id) REFERENCES fuel (id)');
        $this->addSql('ALTER TABLE garage ADD CONSTRAINT FK_9F26610BDB77003 FOREIGN KEY (professional_id) REFERENCES professional (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE advert DROP FOREIGN KEY FK_54F1F40B97C79677');
        $this->addSql('ALTER TABLE advert DROP FOREIGN KEY FK_54F1F40BC4FFF555');
        $this->addSql('DROP TABLE advert');
        $this->addSql('DROP TABLE fuel');
        $this->addSql('DROP TABLE garage');
    }
}
