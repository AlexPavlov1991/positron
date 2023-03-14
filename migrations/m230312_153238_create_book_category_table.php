<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_category}}`.
 */
class m230312_153238_create_book_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_category}}', [
            'book_id' => $this->integer(),
            'category_id' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-book_category-book_id',
            'book_category',
            'book_id',
            'book',
            'id'
        );

        $this->addForeignKey(
            'fk-book_category-category_id',
            'book_category',
            'category_id',
            'category',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book_category}}');
    }
}
