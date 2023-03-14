<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use app\models\BookCategory;

class Book extends \yii\db\ActiveRecord
{
    protected $categories = [];

    public static function tableName()
    {
        return 'book';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['title', 'isbn', 'status'], 'required'],
            [['page_count'], 'integer'],
            ['isbn', 'unique', 'targetClass' => self::class, 'targetAttribute' => 'isbn', 'message' => 'Поле isbn должно быть уникальным'],
            [['published_date', 'created_at', 'updated_at', 'thumbnail_url', 'categories'], 'safe'],
            [['short_description', 'long_description', 'isbn'], 'string'],
            [['title', 'status', 'authors'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'isbn' => 'Isbn',
            'page_count' => 'Page Count',
            'published_date' => 'Published Date',
            'thumbnail_url' => 'Thumbnail Url',
            'short_description' => 'Short Description',
            'long_description' => 'Long Description',
            'status' => 'Status',
            'authors' => 'Authors',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getBookCategories()
    {
        return $this->hasMany(BookCategory::class, ['book_id' => 'id']);
    }

    public function setCategories($categoryId)
    {
        $this->categories[] = $categoryId;
    }

    public function getCategories()
    {
        return ArrayHelper::getColumn($this->getBookCategories()->all(), 'category_id');
    }

    public function afterSave($insert, $changedAttributes)
    {
        BookCategory::deleteAll(['book_id' => $this->id]);
        $values = [];
        if (is_array($this->categories) || is_object($this->categories)) {
            foreach ($this->categories as $id) {
                $values[] = [$this->id, $id];
            }
        }

        if (empty($values)) {
            $values[] = [$this->id, 1];
        }

        self::getDb()->createCommand()
            ->batchInsert(BookCategory::tableName(), ['book_id', 'category_id'], $values)->execute();
    
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        $thumbnail_url_path = Yii::getAlias("@webroot/resources/images/thumbnails/{$this->thumbnail_url}");
        if (is_file($thumbnail_url_path)) {
            unlink($thumbnail_url_path);
        }

        BookCategory::deleteAll(['book_id' => $this->id]);

        return parent::beforeDelete();
    }
}
