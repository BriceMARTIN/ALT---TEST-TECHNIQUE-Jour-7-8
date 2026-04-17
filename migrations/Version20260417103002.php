<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260417103002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access_requests (id INT AUTO_INCREMENT NOT NULL, business_justification LONGTEXT NOT NULL, status ENUM(\'pending\',\'approved\',\'rejected\') DEFAULT \'pending\', requested_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, processed_at DATETIME DEFAULT NULL, processing_notes LONGTEXT DEFAULT NULL, user_id INT NOT NULL, tool_id INT NOT NULL, processed_by INT DEFAULT NULL, INDEX IDX_169017608F7B22CC (tool_id), INDEX IDX_16901760888A646A (processed_by), INDEX idx_requests_status (status), INDEX idx_requests_user (user_id), INDEX idx_requests_date (requested_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, color_hex VARCHAR(7) DEFAULT \'#6366f1\' NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_3AF346685E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE cost_tracking (id INT AUTO_INCREMENT NOT NULL, month_year DATE NOT NULL, total_monthly_cost NUMERIC(10, 2) NOT NULL, active_users_count INT DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, tool_id INT NOT NULL, INDEX IDX_1E5C21A98F7B22CC (tool_id), INDEX idx_cost_month_tool (month_year, tool_id), UNIQUE INDEX unique_tool_month (tool_id, month_year), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE tools (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, vendor VARCHAR(100) DEFAULT NULL, website_url VARCHAR(255) DEFAULT NULL, monthly_cost NUMERIC(10, 2) NOT NULL, active_users_count INT DEFAULT 0 NOT NULL, owner_department ENUM(\'Engineering\',\'Sales\',\'Marketing\',\'HR\',\'Finance\',\'Operations\',\'Design\') NOT NULL, status ENUM(\'active\',\'deprecated\',\'trial\') DEFAULT \'active\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, category_id INT NOT NULL, INDEX idx_tools_category (category_id), INDEX idx_tools_department (owner_department), INDEX idx_tools_status (status), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE usage_logs (id INT AUTO_INCREMENT NOT NULL, session_date DATE NOT NULL, usage_minutes INT DEFAULT 0 NOT NULL, actions_count INT DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, user_id INT NOT NULL, tool_id INT NOT NULL, INDEX IDX_5B25D447A76ED395 (user_id), INDEX IDX_5B25D4478F7B22CC (tool_id), INDEX idx_usage_date_tool (session_date, tool_id), INDEX idx_usage_user_date (user_id, session_date), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user_tool_access (id INT AUTO_INCREMENT NOT NULL, granted_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, revoked_at DATETIME DEFAULT NULL, status ENUM(\'active\',\'revoked\') DEFAULT \'active\', user_id INT NOT NULL, tool_id INT NOT NULL, granted_by INT NOT NULL, revoked_by INT DEFAULT NULL, INDEX IDX_CA23EEDDA5FB753F (granted_by), INDEX IDX_CA23EEDD8E5493E3 (revoked_by), INDEX idx_access_user (user_id), INDEX idx_access_tool (tool_id), INDEX idx_access_granted_date (granted_at), INDEX idx_access_status (status), UNIQUE INDEX unique_user_tool_active (user_id, tool_id, status), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, department ENUM(\'Engineering\',\'Sales\',\'Marketing\',\'HR\',\'Finance\',\'Operations\',\'Design\') NOT NULL, role ENUM(\'employee\',\'manager\',\'admin\') DEFAULT \'employee\', status ENUM(\'active\',\'inactive\') DEFAULT \'active\', hire_date DATE DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX idx_users_department (department), INDEX idx_users_status (status), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE access_requests ADD CONSTRAINT FK_16901760A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE access_requests ADD CONSTRAINT FK_169017608F7B22CC FOREIGN KEY (tool_id) REFERENCES tools (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE access_requests ADD CONSTRAINT FK_16901760888A646A FOREIGN KEY (processed_by) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cost_tracking ADD CONSTRAINT FK_1E5C21A98F7B22CC FOREIGN KEY (tool_id) REFERENCES tools (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tools ADD CONSTRAINT FK_EAFADE7712469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE usage_logs ADD CONSTRAINT FK_5B25D447A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usage_logs ADD CONSTRAINT FK_5B25D4478F7B22CC FOREIGN KEY (tool_id) REFERENCES tools (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tool_access ADD CONSTRAINT FK_CA23EEDDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tool_access ADD CONSTRAINT FK_CA23EEDD8F7B22CC FOREIGN KEY (tool_id) REFERENCES tools (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tool_access ADD CONSTRAINT FK_CA23EEDDA5FB753F FOREIGN KEY (granted_by) REFERENCES users (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE user_tool_access ADD CONSTRAINT FK_CA23EEDD8E5493E3 FOREIGN KEY (revoked_by) REFERENCES users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access_requests DROP FOREIGN KEY FK_16901760A76ED395');
        $this->addSql('ALTER TABLE access_requests DROP FOREIGN KEY FK_169017608F7B22CC');
        $this->addSql('ALTER TABLE access_requests DROP FOREIGN KEY FK_16901760888A646A');
        $this->addSql('ALTER TABLE cost_tracking DROP FOREIGN KEY FK_1E5C21A98F7B22CC');
        $this->addSql('ALTER TABLE tools DROP FOREIGN KEY FK_EAFADE7712469DE2');
        $this->addSql('ALTER TABLE usage_logs DROP FOREIGN KEY FK_5B25D447A76ED395');
        $this->addSql('ALTER TABLE usage_logs DROP FOREIGN KEY FK_5B25D4478F7B22CC');
        $this->addSql('ALTER TABLE user_tool_access DROP FOREIGN KEY FK_CA23EEDDA76ED395');
        $this->addSql('ALTER TABLE user_tool_access DROP FOREIGN KEY FK_CA23EEDD8F7B22CC');
        $this->addSql('ALTER TABLE user_tool_access DROP FOREIGN KEY FK_CA23EEDDA5FB753F');
        $this->addSql('ALTER TABLE user_tool_access DROP FOREIGN KEY FK_CA23EEDD8E5493E3');
        $this->addSql('DROP TABLE access_requests');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE cost_tracking');
        $this->addSql('DROP TABLE tools');
        $this->addSql('DROP TABLE usage_logs');
        $this->addSql('DROP TABLE user_tool_access');
        $this->addSql('DROP TABLE users');
    }
}
