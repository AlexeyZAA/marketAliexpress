<?php
/* @var $this yii\web\View */

use yii\widgets\ListView;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\bootstrap\Alert;
use yii\bootstrap\Carousel;

$this->title = 'ТУР БИЛЕТ';
?>

<div class="body-content">
    
 <div class="container">      
     
        <div class="row">
            <div class="col-md-10 blok-zakaz">
                
                
    <?php 
    /*Сообщение любое*/
    echo Alert::widget([
       'options' => [
           'class' => 'alert-info'
       ],
       'body' => '<b><h1>Тут будет Личный кабинет admina</h1>'
    ]);
    ?>

            </div>
        </div>
     </div>
</div>
</div>

