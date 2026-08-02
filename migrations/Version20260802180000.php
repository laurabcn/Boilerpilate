<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260802180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create orders and order_lines tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE orders (id UUID NOT NULL, status VARCHAR(32) NOT NULL, total_cents INT NOT NULL, currency VARCHAR(3) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE order_lines (id SERIAL NOT NULL, order_id UUID NOT NULL, product_sku VARCHAR(64) NOT NULL, quantity INT NOT NULL, unit_price_cents INT NOT NULL, currency VARCHAR(3) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_9CE58B1D8D9F6D38 ON order_lines (order_id)');
        $this->addSql('ALTER TABLE order_lines ADD CONSTRAINT FK_9CE58B1D8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN orders.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_lines DROP CONSTRAINT FK_9CE58B1D8D9F6D38');
        $this->addSql('DROP TABLE order_lines');
        $this->addSql('DROP TABLE orders');
    }
}
