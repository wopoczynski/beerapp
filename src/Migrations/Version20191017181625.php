<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191017181625 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'init';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql(' CREATE TABLE beer
                              (
                                 id         INT auto_increment NOT NULL,
                                 product_id INT NOT NULL,
                                 name       VARCHAR(255) DEFAULT NULL,
                                 size       VARCHAR(255) DEFAULT NULL,
                                 price      VARCHAR(255) DEFAULT NULL,
                                 beer_id    INT DEFAULT NULL,
                                 image_url  VARCHAR(255) DEFAULT NULL,
                                 category   VARCHAR(255) DEFAULT NULL,
                                 abv        VARCHAR(255) DEFAULT NULL,
                                 style      VARCHAR(255) DEFAULT NULL,
                                 attributes VARCHAR(255) DEFAULT NULL,
                                 type       VARCHAR(255) DEFAULT NULL,
                                 brewer     VARCHAR(255) DEFAULT NULL,
                                 country    VARCHAR(255) DEFAULT NULL,
                                 on_sale    TINYINT(1) DEFAULT NULL,
                                 price_per_liter FLOAT DEFAULT NULL,
                                 PRIMARY KEY(id)
                              )
                            DEFAULT CHARACTER SET utf8mb4
                            COLLATE utf8mb4_unicode_ci
                            engine = innodb');
        $this->addSql(' CREATE TABLE brewer
                              (
                                 id        INT auto_increment NOT NULL,
                                 name      VARCHAR(255) DEFAULT NULL,
                                 PRIMARY KEY(id)
                              )
                            DEFAULT CHARACTER SET utf8mb4
                            COLLATE utf8mb4_unicode_ci
                            engine = innodb  ');
        $this->addSql('CREATE INDEX beer_id ON beer(id)');
        $this->addSql('CREATE INDEX beer_product_id ON beer(product_id)');
        $this->addSql('CREATE INDEX beer_beer_id ON beer(beer_id)');
        $this->addSql('CREATE INDEX brewer_brewer_id ON brewer(id)');
        $this->addSql('CREATE TABLE brewer_to_beer
                             (
                                          id        INT auto_increment NOT NULL,
                                          beer_id   int NOT NULL,
                                          brewer_id int NOT NULL,
                                          PRIMARY KEY (id),
                                          FOREIGN KEY (beer_id) REFERENCES beer(id) ON DELETE CASCADE,
                                          FOREIGN KEY (brewer_id) REFERENCES brewer(id) ON DELETE CASCADE
                             )
                         DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci engine = innodb ');
        $this->addSql('CREATE INDEX brewer_to_beer_id ON brewer_to_beer(id)');
        $this->addSql('CREATE INDEX brewer_to_beer_beer_id ON brewer_to_beer(beer_id)');
        $this->addSql('CREATE INDEX brewer_to_beer_brewer_id ON brewer_to_beer(brewer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE beer');
        $this->addSql('DROP TABLE brewer');
        $this->addSql('DROP TABLE brewer_to_beer');
    }
}
