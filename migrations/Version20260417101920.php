<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260417101920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access_requests CHANGE business_justification business_justification LONGTEXT NOT NULL, CHANGE status status ENUM(\'pending\',\'approved\',\'rejected\') DEFAULT \'pending\', CHANGE requested_at requested_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE processing_notes processing_notes LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE access_requests RENAME INDEX tool_id TO IDX_169017608F7B22CC');
        $this->addSql('ALTER TABLE access_requests RENAME INDEX processed_by TO IDX_16901760888A646A');
        $this->addSql('ALTER TABLE categories CHANGE description description LONGTEXT DEFAULT NULL, CHANGE color_hex color_hex VARCHAR(7) DEFAULT \'#6366f1\' NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE categories RENAME INDEX name TO UNIQ_3AF346685E237E06');
        $this->addSql('ALTER TABLE cost_tracking CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('DROP INDEX idx_tools_active_users ON tools');
        $this->addSql('DROP INDEX idx_tools_cost_desc ON tools');
        $this->addSql('ALTER TABLE tools CHANGE description description LONGTEXT DEFAULT NULL, CHANGE owner_department owner_department ENUM(\'Engineering\',\'Sales\',\'Marketing\',\'HR\',\'Finance\',\'Operations\',\'Design\') NOT NULL, CHANGE status status ENUM(\'active\',\'deprecated\',\'trial\') DEFAULT \'active\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE usage_logs CHANGE usage_minutes usage_minutes INT DEFAULT 0 NOT NULL, CHANGE actions_count actions_count INT DEFAULT 0 NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE usage_logs RENAME INDEX tool_id TO IDX_5B25D4478F7B22CC');
        $this->addSql('ALTER TABLE user_tool_access CHANGE granted_at granted_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE status status ENUM(\'active\',\'revoked\') DEFAULT \'active\'');
        $this->addSql('ALTER TABLE user_tool_access RENAME INDEX granted_by TO IDX_CA23EEDDA5FB753F');
        $this->addSql('ALTER TABLE user_tool_access RENAME INDEX revoked_by TO IDX_CA23EEDD8E5493E3');
        $this->addSql('ALTER TABLE users CHANGE department department ENUM(\'Engineering\',\'Sales\',\'Marketing\',\'HR\',\'Finance\',\'Operations\',\'Design\') NOT NULL, CHANGE role role ENUM(\'employee\',\'manager\',\'admin\') DEFAULT \'employee\', CHANGE status status ENUM(\'active\',\'inactive\') DEFAULT \'active\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE users RENAME INDEX email TO UNIQ_1483A5E9E7927C74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access_requests CHANGE business_justification business_justification TEXT NOT NULL, CHANGE status status ENUM(\'pending\', \'approved\', \'rejected\') DEFAULT \'pending\', CHANGE requested_at requested_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE processing_notes processing_notes TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE access_requests RENAME INDEX idx_16901760888a646a TO processed_by');
        $this->addSql('ALTER TABLE access_requests RENAME INDEX idx_169017608f7b22cc TO tool_id');
        $this->addSql('ALTER TABLE categories CHANGE description description TEXT DEFAULT NULL, CHANGE color_hex color_hex VARCHAR(7) DEFAULT \'#6366f1\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE categories RENAME INDEX uniq_3af346685e237e06 TO name');
        $this->addSql('ALTER TABLE cost_tracking CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE tools CHANGE description description TEXT DEFAULT NULL, CHANGE owner_department owner_department ENUM(\'Engineering\', \'Sales\', \'Marketing\', \'HR\', \'Finance\', \'Operations\', \'Design\') NOT NULL, CHANGE status status ENUM(\'active\', \'deprecated\', \'trial\') DEFAULT \'active\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('CREATE INDEX idx_tools_active_users ON tools (active_users_count)');
        $this->addSql('CREATE INDEX idx_tools_cost_desc ON tools (monthly_cost)');
        $this->addSql('ALTER TABLE usage_logs CHANGE usage_minutes usage_minutes INT DEFAULT 0, CHANGE actions_count actions_count INT DEFAULT 0, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE usage_logs RENAME INDEX idx_5b25d4478f7b22cc TO tool_id');
        $this->addSql('ALTER TABLE user_tool_access CHANGE granted_at granted_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE status status ENUM(\'active\', \'revoked\') DEFAULT \'active\'');
        $this->addSql('ALTER TABLE user_tool_access RENAME INDEX idx_ca23eedd8e5493e3 TO revoked_by');
        $this->addSql('ALTER TABLE user_tool_access RENAME INDEX idx_ca23eedda5fb753f TO granted_by');
        $this->addSql('ALTER TABLE users CHANGE department department ENUM(\'Engineering\', \'Sales\', \'Marketing\', \'HR\', \'Finance\', \'Operations\', \'Design\') NOT NULL, CHANGE role role ENUM(\'employee\', \'manager\', \'admin\') DEFAULT \'employee\', CHANGE status status ENUM(\'active\', \'inactive\') DEFAULT \'active\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_1483a5e9e7927c74 TO email');
    }
}
