<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class UserConfig extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_configs';
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
            [['user_id', 'key'], 'required'],
            ['user_id', 'integer'],
            ['key', 'string', 'max' => 100],
            ['value', 'string'],
            ['user_id', 'exist', 'targetClass' => UserApi::class, 'targetAttribute' => 'id'],
            [['user_id', 'key'], 'unique', 'targetAttribute' => ['user_id', 'key']],
        ];
    }

    public function fields()
    {
        return ['id', 'user_id', 'key', 'value', 'created_at', 'updated_at'];
    }

    public function getUser()
    {
        return $this->hasOne(UserApi::class, ['id' => 'user_id']);
    }
}
