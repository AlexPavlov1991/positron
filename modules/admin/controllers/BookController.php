<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Book;
use app\models\BookSearch;
use app\models\Category;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    // public function behaviors()
    // {
    //     return array_merge(
    //         parent::behaviors(),
    //         [
    //             'verbs' => [
    //                 'class' => VerbFilter::className(),
    //                 'actions' => [
    //                     'delete' => ['POST'],
    //                 ],
    //             ],
    //         ]
    //     );
    // }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $categories = ArrayHelper::getColumn(Category::find()->where(['in', 'id', $model->categories])->all(), 'title');

        return $this->render('view', [
            'model' => $model,
            'categories' => $categories
        ]);
    }

    public function actionCreate()
    {
        $model = new Book();
        $categories = Category::find()->all();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($this->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->thumbnail_url = UploadedFile::getInstance($model, 'thumbnail_url')) { 
                $model->thumbnail_url->saveAs("resources/images/thumbnails/{$model->thumbnail_url->baseName}.{$model->thumbnail_url->extension}");
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => $categories
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $categories = Category::find()->all();
        $thumbnail_url = $model->thumbnail_url;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($this->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->thumbnail_url = UploadedFile::getInstance($model, 'thumbnail_url')) {
                $thumbnail_url_path = Yii::getAlias("@webroot/resources/images/thumbnails/{$thumbnail_url}");
                if (is_file($thumbnail_url_path)) {
                    unlink($thumbnail_url_path);
                }
                $model->thumbnail_url->saveAs("resources/images/thumbnails/{$model->thumbnail_url->baseName}.{$model->thumbnail_url->extension}");
            } else {
                $model->thumbnail_url = $thumbnail_url;
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'categories' => $categories
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
