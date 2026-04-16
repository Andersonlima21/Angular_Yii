<?php

use yii\db\Migration;

class m260415_000002_create_user_profiles_table extends Migration
{
    public function safeUp()
    {
        // Tabela com dados adicionais do usuário (1:1 com users).
        $this->createTable('user_profiles', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer()->notNull()->unique(),
            'phone'      => $this->string(30),
            'birth_date' => $this->string(10),   // YYYY-MM-DD
            'bio'        => $this->text(),
            'avatar_url' => $this->string(500),
            'created_at' => $this->string()->notNull(),
            'updated_at' => $this->string()->notNull(),
            'FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('user_profiles');
    }
}
