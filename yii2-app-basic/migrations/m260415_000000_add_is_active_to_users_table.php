<?php

use yii\db\Migration;

class m260415_000000_add_is_active_to_users_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('users', 'is_active', $this->boolean()->notNull()->defaultValue(true));
    }

    public function safeDown()
    {
        $this->dropColumn('users', 'is_active');
    }
}
