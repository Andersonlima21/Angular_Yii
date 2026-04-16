<?php

use yii\db\Migration;

/**
 * Substitui os campos de preferência de UI (theme, language, timezone, etc.)
 * por campos de registro de aprendizado: platform, stack, certificate_url.
 *
 * SQLite não suporta DROP COLUMN nem ALTER TYPE, então o caminho é:
 * rename → create nova → drop antiga.
 * Dados existentes são descartados (schema incompatível).
 */
class m260416_000001_redesign_user_profile_settings_table extends Migration
{
    public function safeUp()
    {
        $this->renameTable('user_profile_settings', 'user_profile_settings_old');

        $this->createTable('user_profile_settings', [
            'id'              => $this->primaryKey(),
            'user_profile_id' => $this->integer()->notNull(),
            'platform'        => $this->string(20)->notNull(),
            'stack'           => $this->string(20)->notNull(),
            'certificate_url' => $this->string(500),
            'created_at'      => $this->string()->notNull(),
            'updated_at'      => $this->string()->notNull(),
            'FOREIGN KEY (user_profile_id) REFERENCES user_profiles (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        $this->createIndex(
            'idx-ups-user_profile_id',
            'user_profile_settings',
            'user_profile_id'
        );

        $this->dropTable('user_profile_settings_old');
    }

    public function safeDown()
    {
        $this->renameTable('user_profile_settings', 'user_profile_settings_old');

        $this->createTable('user_profile_settings', [
            'id'                    => $this->primaryKey(),
            'user_profile_id'       => $this->integer()->notNull(),
            'theme'                 => $this->string(20)->notNull()->defaultValue('light'),
            'language'              => $this->string(10)->notNull()->defaultValue('pt-BR'),
            'timezone'              => $this->string(50)->notNull()->defaultValue('America/Sao_Paulo'),
            'notifications_enabled' => $this->boolean()->notNull()->defaultValue(true),
            'url'                   => $this->string(500),
            'created_at'            => $this->string()->notNull(),
            'updated_at'            => $this->string()->notNull(),
            'FOREIGN KEY (user_profile_id) REFERENCES user_profiles (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        $this->createIndex(
            'idx-user_profile_settings-user_profile_id',
            'user_profile_settings',
            'user_profile_id'
        );

        $this->dropTable('user_profile_settings_old');
    }
}
