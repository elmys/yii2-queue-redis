<?php

namespace elmys\yii2\queueRedis\widgets\queueManager;

use yii\base\Widget;
use yii\data\ArrayDataProvider;

class QueueManager extends Widget
{
    public ArrayDataProvider $provider;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('index', [
            'provider' => $this->provider
        ]);
    }
}
