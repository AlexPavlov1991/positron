<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Option;
use app\models\Book;
use app\models\Category;

class BookParsingController extends Controller
{
    public $source;

    public function options($actionID)
    {
        return ['source'];
    }
    
    public function optionAliases()
    {
        return ['s' => 'source'];
    }
    
    public function actionIndex()
    {
        echo self::class . PHP_EOL;
    }

    public function actionGo()
    {
        if (!$this->source) {
            $this->source = Option::find()->where(['name' => 'book_source_url'])->one()->value ?? "";
        }

        if (!$this->source) {
            die("Parsing source empty" . PHP_EOL);
        }; 

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->source);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $books = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code < 200 || $http_code >= 300) {
            print_r(curl_getinfo($ch)); die;
        }

        try {
            $books = json_decode($books, 1);
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage() . PHP_EOL);
        }
        
        foreach ($books as $key => $book) {
            // authors
            $books[$key]['authors'] = implode(', ', $book['authors']);

            // categories
            if (empty($book['categories'])) {
                $books[$key]['categories'] = [1];
            } else {
                $category_ids = [];
                foreach ($book['categories'] as $category_title) {
                    $category_title = trim($category_title);
                    if (!$category_title) {
                        continue;
                    }
                    $categoryModel = Category::find()->where(['title' => $category_title])->one();
                    $category_id = $categoryModel->id ?? null;
                    if ($category_id) {
                        $category_ids[] = $category_id;
                    } else {
                        $categoryModel = new Category();
                        $categoryModel->title = $category_title;
                        if (!$categoryModel->save()) {
                            echo "save category error [{$category_title}]"  . PHP_EOL;
                        }
                        $category_ids[] = $categoryModel->id;
                        
                    }
                }
                // print_r($category_ids);
                $books[$key]['categories'] = $category_ids;
            }

            // published_date
            if (!empty($book['publishedDate']['date'])) {
                $books[$key]['publishedDate'] = substr($book['publishedDate']['date'], 0, strpos($book['publishedDate']['date'], 'T'));
            } else {
                $books[$key]['publishedDate'] = null;
            }

        }

        foreach ($books as $book) {
            if (empty($book['isbn'])) {
                echo "isbn empty" . PHP_EOL;
                continue;
            }

            $bookModel = Book::find()->where(['isbn' => $book['isbn']])->one();
            if ($bookModel) {
                echo "isbn already exists [{$bookModel->isbn}]" . PHP_EOL;
                continue;
            }

            $bookModel = new Book();
            $bookModel->title = $book['title'];
            $bookModel->isbn = $book['isbn'];
            $bookModel->published_date = $book['publishedDate'];
            $bookModel->page_count = $book['pageCount'] ?? 0;
            $bookModel->thumbnail_url = $book['thumbnailUrl'] ?? '';
            $bookModel->short_description = $book['shortDescription'] ?? '';
            $bookModel->long_description = $book['longDescription'] ?? '';
            $bookModel->status = $book['status'] ?? '';
            $bookModel->authors = $book['authors'];
            $bookModel->categories = $book['categories'];

            if (!$bookModel->save()) {
                echo "save book error [{$book['isbn']}]" . PHP_EOL;
                print_r($book);
            }
        }
    }
}