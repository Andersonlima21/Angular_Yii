<?php

use yii\db\Migration;

class m260415_000001_create_user_configs_table extends Migration
{
    public function safeUp()
    {
        // SQLite não suporta ADD CONSTRAINT depois do CREATE TABLE.
        // A FK precisa ser declarada inline via chave "pseudo-coluna".
        $this->createTable('user_configs', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer()->notNull(),
            'key'        => $this->string(100)->notNull(),
            'value'      => $this->text(),
            'created_at' => $this->string()->notNull(),
            'updated_at' => $this->string()->notNull(),
            'FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        $this->createIndex(
            'idx-user_configs-user_id-key',
            'user_configs',
            ['user_id', 'key'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropTable('user_configs');
    }
}
