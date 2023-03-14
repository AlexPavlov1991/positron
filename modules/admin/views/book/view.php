<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Book $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Categories',
                'value' => implode(', ', $categories)
            ],
            'title',
            'isbn',
            'page_count',
            'published_date',
            // 'thumbnail_url:url',
            [
                'attribute' => 'thumbnail_url',
                'value' => '/resources/images/thumbnails/' . $model->thumbnail_url,
                'format' => ['image', ['width' => 100]]
            ],
            'short_description:ntext',
            'long_description:ntext',
            'status',
            'authors',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
