<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class UserProfile extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_profiles';
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
            ['user_id', 'required'],
            ['user_id', 'integer'],
            ['user_id', 'unique'],
            ['user_id', 'exist', 'targetClass' => UserApi::class, 'targetAttribute' => 'id'],
            [['phone'], 'string', 'max' => 30],
            [['birth_date'], 'date', 'format' => 'php:Y-m-d'],
            [['avatar_url'], 'string', 'max' => 500],
            [['bio'], 'string'],
        ];
    }

    public function fields()
    {
        return ['id', 'user_id', 'phone', 'birth_date', 'bio', 'avatar_url', 'created_at', 'updated_at'];
    }

    public function getUser()
    {
        return $this->hasOne(UserApi::class, ['id' => 'user_id']);
    }

    public function getSettings()
    {
        return $this->hasMany(UserProfileSetting::class, ['user_profile_id' => 'id']);
    }
}
