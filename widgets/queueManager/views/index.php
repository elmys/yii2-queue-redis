<?php

/* @var $provider ArrayDataProvider */

use yii\data\ArrayDataProvider;

$this->registerCss('
.bar {
  border: 1px solid #666;
  height: 20px;
  width: 100%;
  
  .in {
    animation: fill ' . Yii::$app->params['refreshPageSeconds'] . 's linear reverse 1;
    height: 100%;
    background-color: silver;
  }
}

@keyframes fill {
  0% {
    width: 0%;
  }
  100% {
    width: 100%;
  }
}
');

$this->registerMetaTag(
    ['http-equiv' => 'Refresh', 'content' => Yii::$app->params['refreshPageSeconds']]
);

?>


<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<div class="row">
    <div class="col-12">
        <div class="bar">
            <div class="in"></div>
        </div>
        <br>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $provider,
            'tableOptions' => [
                'class' => 'table admin-queues'
            ],
            'rowOptions' => function ($model, $key, $index, $widget) {
                return ['class' => $model['group']];
            },
            'columns' => [
                [
                    'label' => \Yii::t('queueRedis', 'Queue name'),
                    'format' => 'raw',
                    'value' => function ($model) {
                        //return '<a href="#" class="q-name" title="Кликните для копирования key name (Red Insight)">' . $model['name'] . '.*</a>' . '&nbsp<span class="badge badge-sm filled">' . $model['group'] . '</span>';
                        return $model['name'] . '.*&nbsp<span class="badge badge-sm filled">' . $model['group'] . '</span>';
                    }
                ],
                [
                    'label' => \Yii::t('queueRedis', 'Total'),
                    'value' => 'total',
                ],
                [
                    'label' => \Yii::t('queueRedis', 'Postponed'),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<span class="badge badge-sm ' . ($model['delayed'] != '0' ? 'filled' : '') . '">' . $model['delayed'] . '</span>';
                    },
                ],
                [
                    'label' => \Yii::t('queueRedis', 'Reserved'),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<span class="badge badge-sm ' . ($model['reserved'] != '0' ? 'filled' : '') . '">' . $model['reserved'] . '</span>';
                    },
                ],
                [
                    'label' => \Yii::t('queueRedis', 'Waiting'),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<span class="badge badge-sm ' . ($model['waiting'] != '0' ? 'filled' : '') . '">' . $model['waiting'] . '</span>';
                    },
                ],
                [
                    'format' => 'raw',
                    'value' => function ($model) {
                        return \yii\helpers\Html::a(\Yii::t('queueRedis', 'Clean Up'), ['queue-clear', 'qid' => $model['name'], 'layer' => \elmys\yii2\queueRedis\actions\QueueRedisAction::$appLayer], ['class' => 'btn btn-outline-primary', 'data' => [
                            'confirm' => \Yii::t('queueRedis', 'Continue?'),
                            'method' => 'post',
                        ],]);
                    }
                ],
            ]
        ]); ?>
        <div class="bar">
            <div class="in"></div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
    $('.progress-bar-fill').delay(1000).queue(function () {
        $(this).css('width', '100%')
    });

JS, yii\web\View::POS_END);
?>

<?php $this->endContent(); ?>

