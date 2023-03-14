<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m230312_153227_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'title' => $this->string()->notNull(),
            'created_at' => $this->datetime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->datetime()->defaultExpression('NOW()')
        ]);

        $this->insert('{{%category}}', [
            'title' => 'Новинки'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%category}}');
    }
}
