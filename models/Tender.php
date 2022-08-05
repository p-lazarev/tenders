<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tender".
 *
 * @property int $id
 * @property string|null $tender_id
 * @property string|null $description
 * @property float|null $value_amount
 * @property string|null $date_modified
 */
class Tender extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tender';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['value_amount'], 'number'],
            [['tender_id', 'date_modified'], 'string', 'max' => 32],
            [['tender_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tender_id' => 'Tender ID',
            'description' => 'Description',
            'value_amount' => 'Value Amount',
            'date_modified' => 'Date Modified',
        ];
    }
}
