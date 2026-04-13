<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user and reset_token tables, messenger_messages table';
    }

    public function up(Schema $schema): void
    {
        // User table
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (
                id            UUID        NOT NULL,
                email         VARCHAR(180) NOT NULL,
                roles         JSON        NOT NULL,
                password      VARCHAR(255) NOT NULL,
                full_name     VARCHAR(100) NOT NULL,
                created_at    TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at    TIMESTAMP(0) WITHOUT TIME ZONE,
                last_login_at TIMESTAMP(0) WITHOUT TIME ZONE,
                is_verified   BOOLEAN     NOT NULL DEFAULT FALSE,
                PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".last_login_at IS \'(DC2Type:datetime_immutable)\'');

        // ResetToken table
        $this->addSql(<<<'SQL'
            CREATE TABLE reset_token (
                id         UUID         NOT NULL,
                user_id    UUID         NOT NULL,
                token      VARCHAR(64)  NOT NULL,
                expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                used       BOOLEAN      NOT NULL DEFAULT FALSE,
                PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_452C9EC55F37A13B ON reset_token (token)');
        $this->addSql('CREATE INDEX IDX_452C9EC5A76ED395 ON reset_token (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_token.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reset_token.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reset_token.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_token.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reset_token ADD CONSTRAINT FK_reset_token_user FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Messenger messages table
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (
                id              BIGSERIAL    NOT NULL,
                body            TEXT         NOT NULL,
                headers         TEXT         NOT NULL,
                queue_name      VARCHAR(190) NOT NULL,
                created_at      TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                available_at    TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                delivered_at    TIMESTAMP(0) WITHOUT TIME ZONE,
                PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_messenger_queue ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_messenger_available ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_messenger_delivered ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');

        // Notify trigger for Messenger
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger
            AFTER INSERT OR UPDATE ON messenger_messages
            FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages()
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages');
        $this->addSql('DROP FUNCTION IF EXISTS notify_messenger_messages');
        $this->addSql('ALTER TABLE reset_token DROP CONSTRAINT FK_reset_token_user');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE reset_token');
        $this->addSql('DROP TABLE "user"');
    }
}
