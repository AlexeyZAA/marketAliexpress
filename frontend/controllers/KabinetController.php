<?php

namespace frontend\controllers;

class KabinetController extends \yii\web\Controller
{
    public function actionIndex()
    {
     /*Тут делаем проверку ролей на доступ к своему личному кабинету*/
        
        if (\Yii::$app->user->can('administrator')) {
            return $this->render('indexadmin');
        }
        
        if (\Yii::$app->user->can('moderator'))  {
            return $this->render('index');
        }
        
        if (\Yii::$app->user->can('redaktor')) {
            return $this->render('index');
        }
    }

}
