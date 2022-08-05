<?php

use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tenders';
?>

<div class="site-index">

    <h1><?= $this->title ?></h1>

    <div class="body-content">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
            'columns' => [
                'id',
                [
                    'attribute' => 'tender_id',
                    'value' => function($tender){
                        return \yii\helpers\Html::a($tender->tender_id, "https://public.api.openprocurement.org/api/0/tenders/{$tender->tender_id}", ['target' => '_blank']);
                    },
                    'format' => 'raw',
                ],
                'description',
                'value_amount',
                'date_modified',
            ],
        ]) ?>

    </div>
</div>
