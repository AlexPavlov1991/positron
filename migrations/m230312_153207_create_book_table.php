<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book}}`.
 */
class m230312_153207_create_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'isbn' => $this->string()->notNull(),
            'page_count' => $this->integer(),
            'published_date' => $this->date(),
            'thumbnail_url' => $this->string(),
            'short_description' => $this->text(),
            'long_description' => $this->text(),
            'status' => $this->string()->notNull(),
            'authors' => $this->string(),
            'created_at' => $this->datetime()->notNull(),
            'updated_at' => $this->datetime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book}}');
    }
}
