<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Page */

$this->title = 'Update Page: ' . $model->page_id;
$this->params['breadcrumbs'][] = ['label' => 'Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->page_id, 'url' => ['view', 'id' => $model->page_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="page-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
