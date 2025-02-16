<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210715194111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE documentados (id INT AUTO_INCREMENT NOT NULL, localidad_id INT DEFAULT NULL, planta_id INT DEFAULT NULL, claim VARCHAR(255) DEFAULT NULL, codigo VARCHAR(255) DEFAULT NULL, numero VARCHAR(255) DEFAULT NULL, cantidad VARCHAR(255) DEFAULT NULL, fecha_notificacion DATE DEFAULT NULL, perdida_sin_flete VARCHAR(255) DEFAULT NULL, perdida_con_flete VARCHAR(255) DEFAULT NULL, area VARCHAR(255) DEFAULT NULL, estatus VARCHAR(255) DEFAULT NULL, documentacion_faltante LONGTEXT DEFAULT NULL, INDEX IDX_58A8907667707C89 (localidad_id), INDEX IDX_58A89076981981BF (planta_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE documentados ADD CONSTRAINT FK_58A8907667707C89 FOREIGN KEY (localidad_id) REFERENCES localidad (id)');
        $this->addSql('ALTER TABLE documentados ADD CONSTRAINT FK_58A89076981981BF FOREIGN KEY (planta_id) REFERENCES planta (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE documentados');
    }
}
