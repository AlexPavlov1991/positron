<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%option}}`.
 */
class m230312_154253_create_option_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%option}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'name' => $this->string()->notNull(),
            'value' => $this->string(),
            'created_at' => $this->datetime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->datetime()->defaultExpression('NOW()'),
        ]);

        $this->batchInsert('{{%option}}',
            ['title', 'name', 'value'],
            [
                ['Кол-во элементов на страницу (для книг)', 'book_page_limit', '10'],
                ['Email адрес получателя сообщения с формы обратной связи', 'email', 'alexpavlov.it@gmail.com'],
                ['Источник данных для парсинга (url)', 'book_source_url', 'https://gitlab.com/prog-positron/test-app-vacancy/-/raw/master/books.json']
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%option}}');
    }
}
