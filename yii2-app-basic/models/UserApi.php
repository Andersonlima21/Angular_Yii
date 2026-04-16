<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class UserApi extends ActiveRecord
{
    public static function tableName()
    {
        return 'users';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression("datetime('now')"),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['name', 'email'], 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'unique'],
            ['is_active', 'boolean'],
            ['is_active', 'default', 'value' => true],
        ];
    }

    public function fields()
    {
        return ['id', 'name', 'email', 'is_active', 'created_at', 'updated_at'];
    }

    public function getConfigs()
    {
        return $this->hasMany(UserConfig::class, ['user_id' => 'id']);
    }
}
