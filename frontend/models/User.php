<?php

namespace frontend\models;
use yii\db\ActiveRecord;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $fio
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property string $photo
 * @property string $visitkazakaz
 * @property string $visitkaispol
 * @property integer $sex
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property Objavlenie[] $objavlenies
 * @property UserEmailConfirmToken[] $userEmailConfirmTokens
 * @property UserOauthKey[] $userOauthKeys
 * @property UserPasswordResetToken[] $userPasswordResetTokens
 * @property Zakaz[] $zakazs
 */
class User extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'fio', 'auth_key', 'password_hash', 'created_at', 'updated_at'], 'required'],
            [['visitkazakaz', 'visitkaispol'], 'string'],
            [['sex', 'status', 'zakazchik', 'ispolnitel', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'email', 'photo'], 'string', 'max' => 255],
            [['fio'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'fio' => 'Fio',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'email' => 'Email',
            'photo' => 'Photo',
            'visitkazakaz' => 'Visitkazakaz',
            'visitkaispol' => 'Visitkaispol',
            'sex' => 'Sex',
            'status' => 'Status',
            'zakazchik' => 'Zakazchik', 
            'ispolnitel' => 'Ispolnitel',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObjavlenies()
    {
        return $this->hasMany(Objavlenie::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEmailConfirmTokens()
    {
        return $this->hasMany(UserEmailConfirmToken::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserOauthKeys()
    {
        return $this->hasMany(UserOauthKey::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPasswordResetTokens()
    {
        return $this->hasMany(UserPasswordResetToken::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZakazs()
    {
        return $this->hasMany(Zakaz::className(), ['user_id' => 'id']);
    }
}
