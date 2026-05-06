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
            // 'required' só se aplica no SCENARIO_DEFAULT (criação). No 'update', campos são opcionais.
            [['name', 'email'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['name', 'email'], 'string', 'max' => 255],
            ['email', 'email'],
            // unique exclui o próprio registro automaticamente quando isNewRecord = false (findOne).
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
