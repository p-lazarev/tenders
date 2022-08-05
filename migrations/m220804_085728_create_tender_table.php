<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tender}}`.
 */
class m220804_085728_create_tender_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tender}}', [
            'id' => $this->primaryKey(),
            'tender_id' => $this->string(32)->unique(),
            'description' => $this->text(),
            'value_amount' => $this->decimal(20, 2),
            'date_modified' => $this->string(32),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tender}}');
    }
}