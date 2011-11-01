<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20111024220034 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE event__events ADD city VARCHAR(255) DEFAULT NULL, ADD place VARCHAR(255) DEFAULT NULL, ADD date DATETIME DEFAULT NULL, DROP title, CHANGE about about LONGTEXT DEFAULT NULL");
        $this->addSql("ALTER TABLE event__tickets ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, DROP status");
        $this->addSql("CREATE INDEX IDX_66E2955971F7E88B ON event__tickets (event_id)");
        $this->addSql("CREATE INDEX IDX_66E29559A76ED395 ON event__tickets (user_id)");
        $this->addSql("CREATE INDEX IDX_66E295594C3A3BB ON event__tickets (payment_id)");
        $this->addSql("DROP INDEX UNIQ_66E2955971F7E88B ON event__tickets");
        $this->addSql("DROP INDEX UNIQ_66E295594C3A3BB ON event__tickets");
        $this->addSql("DROP INDEX UNIQ_66E29559A76ED395 ON event__tickets");
        $this->addSql("ALTER TABLE payments ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE sum amount NUMERIC(10, 2) NOT NULL");
        $this->addSql("ALTER TABLE payments ADD CONSTRAINT FK_65D29B32A76ED395 FOREIGN KEY (user_id) REFERENCES users(id)");
        $this->addSql("CREATE INDEX IDX_65D29B32A76ED395 ON payments (user_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE event__events ADD title VARCHAR(255) NOT NULL, DROP city, DROP place, DROP date, CHANGE about about LONGTEXT NOT NULL");
        $this->addSql("DROP INDEX IDX_66E2955971F7E88B ON event__tickets");
        $this->addSql("DROP INDEX IDX_66E29559A76ED395 ON event__tickets");
        $this->addSql("DROP INDEX IDX_66E295594C3A3BB ON event__tickets");
        $this->addSql("ALTER TABLE event__tickets ADD status VARCHAR(255) NOT NULL, DROP created_at, DROP updated_at");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_66E2955971F7E88B ON event__tickets (event_id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_66E295594C3A3BB ON event__tickets (payment_id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_66E29559A76ED395 ON event__tickets (user_id)");
        $this->addSql("ALTER TABLE payments DROP FOREIGN KEY FK_65D29B32A76ED395");
        $this->addSql("DROP INDEX IDX_65D29B32A76ED395 ON payments");
        $this->addSql("ALTER TABLE payments DROP created_at, DROP updated_at, CHANGE user_id user_id INT NOT NULL, CHANGE amount sum NUMERIC(10, 0) NOT NULL");
    }
}
