<?php

use yii\db\Migration;

class m260414_000000_create_users_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull()->unique(),
            'created_at' => $this->string()->notNull(),
            'updated_at' => $this->string()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('users');
    }
}
