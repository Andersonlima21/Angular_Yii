<?php

use yii\db\Migration;

class m260415_000003_create_user_profile_settings_table extends Migration
{
    public function safeUp()
    {
        // Tabela complementar ao user_profiles (1:1).
        // NÃO tem user_id: chega no usuário via user_profiles.user_id.
        $this->createTable('user_profile_settings', [
            'id'                     => $this->primaryKey(),
            'user_profile_id'        => $this->integer()->notNull()->unique(),
            'theme'                  => $this->string(20)->notNull()->defaultValue('light'),   // light | dark
            'language'               => $this->string(10)->notNull()->defaultValue('pt-BR'),
            'timezone'               => $this->string(50)->notNull()->defaultValue('America/Sao_Paulo'),
            'notifications_enabled'  => $this->boolean()->notNull()->defaultValue(true),
            'created_at'             => $this->string()->notNull(),
            'updated_at'             => $this->string()->notNull(),
            'FOREIGN KEY (user_profile_id) REFERENCES user_profiles (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('user_profile_settings');
    }
}
