<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class UserProfileSetting extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_profile_settings';
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
            ['user_profile_id', 'required', 'on' => self::SCENARIO_DEFAULT],
            ['user_profile_id', 'integer'],
            ['user_profile_id', 'exist', 'targetClass' => UserProfile::class, 'targetAttribute' => 'id'],
            [['platform', 'stack'], 'required'],
            ['platform', 'in', 'range' => ['alura', 'devmedia', 'udemy']],
            ['stack', 'in', 'range' => ['php', 'xdebug', 'js']],
            ['certificate_url', 'url', 'defaultScheme' => 'https'],
            ['certificate_url', 'string', 'max' => 500],
        ];
    }

    public function fields()
    {
        return ['id', 'user_profile_id', 'platform', 'stack', 'certificate_url', 'created_at', 'updated_at'];
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'user_profile_id']);
    }
}
