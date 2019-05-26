<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use budyaga\users\components\AuthChoice;

?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('users', 'AUTHORISATION')?></div>
    <div class="panel-body">
        <p><?= Yii::t('users', 'VIA_SOCIAL_NETWORKS')?></p>
        <p>
            <?= AuthChoice::widget([
                'baseAuthUrl' => ['/user/auth/index']
            ]) ?>
        </p>
        <p><?= Yii::t('users', 'OR_BY_PASSWORD')?></p>

        <?php $form = ActiveForm::begin(['id' => 'login-widget-form', 'action' => Url::toRoute('/login')]); ?>
            <div