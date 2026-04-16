<?php

use yii\db\Migration;

/**
 * Muda settings de 1:1 para 1:N por profile e adiciona a coluna `url`.
 *
 * SQLite não permite DROP de UNIQUE inline nem ALTER que remova constraints,
 * então o caminho seguro é: rename → createTable nova → INSERT SELECT → drop antiga.
 */
class m260416_000000_alter_user_profile_settings_table extends Migration
{
    public function safeUp()
    {
        $this->renameTable('user_profile_settings', 'user_profile_settings_old');

        $this->createTable('user_profile_settings', [
            'id'                     => $this->primaryKey(),
            // user_profile_id deixa de ser unique — agora um profile pode ter N settings.
            'user_profile_id'        => $this->integer()->notNull(),
            'theme'                  => $this->string(20)->notNull()->defaultValue('light'),
            'language'               => $this->string(10)->notNull()->defaultValue('pt-BR'),
            'timezone'               => $this->string(50)->notNull()->defaultValue('America/Sao_Paulo'),
            'notifications_enabled'  => $this->boolean()->notNull()->defaultValue(true),
            'url'                    => $this->string(500),
            'created_at'             => $this->string()->notNull(),
            'updated_at'             => $this->string()->notNull(),
            'FOREIGN KEY (user_profile_id) REFERENCES user_profiles (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        // Índice não-unique só para acelerar lookup por profile.
        $this->createIndex(
            'idx-user_profile_settings-user_profile_id',
            'user_profile_settings',
            'user_profile_id'
        );

        $this->execute("
            INSERT INTO user_profile_settings
                (id, user_profile_id, theme, language, timezone, notifications_enabled, url, created_at, updated_at)
            SELECT
                id, user_profile_id, theme, language, timezone, notifications_enabled, NULL, created_at, updated_at
            FROM user_profile_settings_old
        ");

        $this->dropTable('user_profile_settings_old');
    }

    public function safeDown()
    {
        // O down só funciona se não houver mais de um setting por profile —
        // caso contrário o INSERT SELECT vai violar o unique novo e o transaction reverte.
        $this->renameTable('user_profile_settings', 'user_profile_settings_old');

        $this->createTable('user_profile_settings', [
            'id'                     => $this->primaryKey(),
            'user_profile_id'        => $this->integer()->notNull()->unique(),
            'theme'                  => $this->string(20)->notNull()->defaultValue('light'),
            'language'               => $this->string(10)->notNull()->defaultValue('pt-BR'),
            'timezone'               => $this->string(50)->notNull()->defaultValue('America/Sao_Paulo'),
            'notifications_enabled'  => $this->boolean()->notNull()->defaultValue(true),
            'created_at'             => $this->string()->notNull(),
            'updated_at'             => $this->string()->notNull(),
            'FOREIGN KEY (user_profile_id) REFERENCES user_profiles (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        $this->execute("
            INSERT INTO user_profile_settings
                (id, user_profile_id, theme, language, timezone, notifications_enabled, created_at, updated_at)
            SELECT
                id, user_profile_id, theme, language, timezone, notifications_enabled, created_at, updated_at
            FROM user_profile_settings_old
        ");

        $this->dropTable('user_profile_settings_old');
    }
}
