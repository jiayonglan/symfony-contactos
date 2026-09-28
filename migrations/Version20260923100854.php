<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260923100854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contacto CHANGE provincia_id provincia_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contacto ADD CONSTRAINT FK_2741493C4E7121AF FOREIGN KEY (provincia_id) REFERENCES provincia (id)');
        $this->addSql('CREATE INDEX IDX_2741493C4E7121AF ON contacto (provincia_id)');
        $this->addSql('ALTER TABLE provincia ADD nombre VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contacto DROP FOREIGN KEY FK_2741493C4E7121AF');
        $this->addSql('DROP INDEX IDX_2741493C4E7121AF ON contacto');
        $this->addSql('ALTER TABLE contacto CHANGE provincia_id provincia_id INT NOT NULL');
        $this->addSql('ALTER TABLE provincia DROP nombre');
    }
}
