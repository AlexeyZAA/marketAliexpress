<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "page".
 *
 * @property integer $page_id
 * @property string $page_url
 * @property string $page_name
 * @property string $page_text
 */
class Page extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'page';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_url', 'page_name', 'page_text'], 'required'],
            [['page_text'], 'string'],
            [['page_url', 'page_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' => 'Page ID',
            'page_url' => 'Page Url',
            'page_name' => 'Page Name',
            'page_text' => 'Page Text',
        ];
    }
}
