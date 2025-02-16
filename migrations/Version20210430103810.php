<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210430103810 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE area (id INT AUTO_INCREMENT NOT NULL, area VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE localidad (id INT AUTO_INCREMENT NOT NULL, localidad VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planta (id INT AUTO_INCREMENT NOT NULL, localidad_id INT NOT NULL, planta VARCHAR(255) NOT NULL, INDEX IDX_5617AD5E67707C89 (localidad_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proveedor (id INT AUTO_INCREMENT NOT NULL, proveedor VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registro (id INT AUTO_INCREMENT NOT NULL, localidad_id INT DEFAULT NULL, planta_id INT DEFAULT NULL, transportista_id INT DEFAULT NULL, area_id INT DEFAULT NULL, proveedor_id INT DEFAULT NULL, ruta_id INT DEFAULT NULL, actualizacion DATE NOT NULL, tipo VARCHAR(100) DEFAULT NULL, descripcion LONGTEXT DEFAULT NULL, referencia VARCHAR(255) NOT NULL, reclamado_usd VARCHAR(255) DEFAULT NULL, reclamado_mxn VARCHAR(255) DEFAULT NULL, aceptado VARCHAR(255) DEFAULT NULL, recuperado VARCHAR(255) DEFAULT NULL, ajustes VARCHAR(255) DEFAULT NULL, reclamo_documentacion VARCHAR(255) DEFAULT NULL, reclamo_proceso VARCHAR(255) DEFAULT NULL, ajuste VARCHAR(255) DEFAULT NULL, cancelado VARCHAR(255) DEFAULT NULL, flete VARCHAR(255) DEFAULT NULL, menores VARCHAR(255) DEFAULT NULL, excedente VARCHAR(255) DEFAULT NULL, estimado VARCHAR(255) DEFAULT NULL, fecha_evento DATE DEFAULT NULL, fecha_asignacion DATE DEFAULT NULL, fecha_documentacion DATE DEFAULT NULL, fecha_emision DATE DEFAULT NULL, fecha_respuesta DATE DEFAULT NULL, fecha_aviso DATE DEFAULT NULL, fecha_aplicacion DATE DEFAULT NULL, estatus VARCHAR(255) DEFAULT NULL, tipo_material VARCHAR(255) DEFAULT NULL, escalado VARCHAR(10) DEFAULT NULL, fecha_escalacion DATE DEFAULT NULL, fecha_resolucion DATE DEFAULT NULL, caja VARCHAR(255) DEFAULT NULL, comentarios LONGTEXT DEFAULT NULL, observaciones LONGTEXT DEFAULT NULL, fecha1 DATE DEFAULT NULL, fecha2 DATE DEFAULT NULL, fecha3 DATE DEFAULT NULL, fecha_cheque DATE DEFAULT NULL, INDEX IDX_397CA85B67707C89 (localidad_id), INDEX IDX_397CA85B981981BF (planta_id), INDEX IDX_397CA85B52F4F166 (transportista_id), INDEX IDX_397CA85BBD0F409C (area_id), INDEX IDX_397CA85BCB305D73 (proveedor_id), INDEX IDX_397CA85BABBC4845 (ruta_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reporte (id INT AUTO_INCREMENT NOT NULL, localidad_id INT DEFAULT NULL, transportista_id INT DEFAULT NULL, claim VARCHAR(255) DEFAULT NULL, tipo VARCHAR(255) DEFAULT NULL, reclamado_usd VARCHAR(255) DEFAULT NULL, reclamado_mxn VARCHAR(255) DEFAULT NULL, excedente_mxn VARCHAR(255) DEFAULT NULL, estimado_mxn VARCHAR(255) DEFAULT NULL, aceptado_mxn VARCHAR(255) DEFAULT NULL, rechazado_mxn VARCHAR(255) DEFAULT NULL, cancelado_mxn VARCHAR(255) DEFAULT NULL, flete VARCHAR(255) DEFAULT NULL, fecha_evento DATE DEFAULT NULL, fecha_emision DATE DEFAULT NULL, fecha1 DATE DEFAULT NULL, fecha2 DATE DEFAULT NULL, fecha3 DATE DEFAULT NULL, fecha_escalacion DATE DEFAULT NULL, fecha_resolucion DATE DEFAULT NULL, area VARCHAR(255) DEFAULT NULL, estatus VARCHAR(255) DEFAULT NULL, observaciones LONGTEXT DEFAULT NULL, actualizacion DATE DEFAULT NULL, INDEX IDX_5CB121467707C89 (localidad_id), INDEX IDX_5CB121452F4F166 (transportista_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ruta (id INT AUTO_INCREMENT NOT NULL, ruta VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transportista (id INT AUTO_INCREMENT NOT NULL, transportista VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, plain_password VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, last_login VARCHAR(255) DEFAULT NULL, activo TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE planta ADD CONSTRAINT FK_5617AD5E67707C89 FOREIGN KEY (localidad_id) REFERENCES localidad (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B67707C89 FOREIGN KEY (localidad_id) REFERENCES localidad (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B981981BF FOREIGN KEY (planta_id) REFERENCES planta (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B52F4F166 FOREIGN KEY (transportista_id) REFERENCES transportista (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85BBD0F409C FOREIGN KEY (area_id) REFERENCES area (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85BCB305D73 FOREIGN KEY (proveedor_id) REFERENCES proveedor (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85BABBC4845 FOREIGN KEY (ruta_id) REFERENCES ruta (id)');
        $this->addSql('ALTER TABLE reporte ADD CONSTRAINT FK_5CB121467707C89 FOREIGN KEY (localidad_id) REFERENCES localidad (id)');
        $this->addSql('ALTER TABLE reporte ADD CONSTRAINT FK_5CB121452F4F166 FOREIGN KEY (transportista_id) REFERENCES transportista (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85BBD0F409C');
        $this->addSql('ALTER TABLE planta DROP FOREIGN KEY FK_5617AD5E67707C89');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B67707C89');
        $this->addSql('ALTER TABLE reporte DROP FOREIGN KEY FK_5CB121467707C89');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B981981BF');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85BCB305D73');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85BABBC4845');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B52F4F166');
        $this->addSql('ALTER TABLE reporte DROP FOREIGN KEY FK_5CB121452F4F166');
        $this->addSql('DROP TABLE area');
        $this->addSql('DROP TABLE localidad');
        $this->addSql('DROP TABLE planta');
        $this->addSql('DROP TABLE proveedor');
        $this->addSql('DROP TABLE registro');
        $this->addSql('DROP TABLE reporte');
        $this->addSql('DROP TABLE ruta');
        $this->addSql('DROP TABLE transportista');
        $this->addSql('DROP TABLE user');
    }
}
