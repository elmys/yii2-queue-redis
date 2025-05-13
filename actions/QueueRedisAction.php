<?php

namespace app\components\queueRedis\actions;

use app\components\queueRedis\models\QueueRedis;
use app\components\queueRedis\widgets\queueManager\QueueManager;
use Throwable;
use yii\base\Action;
use yii\base\UserException;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;

class QueueRedisAction extends Action
{
    const APP_LAYER = 'appLayer';
    public static mixed $appLayer;

    public function init()
    {
        parent::init();
        self::staticInit();
    }

    public static function staticInit()
    {
        $cookies = \Yii::$app->request->cookies;
        $cookies2 = \Yii::$app->response->cookies;
        self::$appLayer = \Yii::$app->request->get('layer');
        if (self::$appLayer === null) {
            if ($cookies->has(self::APP_LAYER)) {
                self::$appLayer = Json::decode($cookies->getValue(self::APP_LAYER));
            } else {
                self::$appLayer = LAYER_STAGE;
                $cookies2->add(new \yii\web\Cookie([
                    'name' => self::APP_LAYER,
                    'value' => self::$appLayer,
                    'expire' => time() + 86400 * 180,
                ]));
            }
        } else {
            $cookies2->add(new \yii\web\Cookie([
                'name' => self::APP_LAYER,
                'value' => self::$appLayer,
                'expire' => time() + 86400 * 180,
            ]));
        }

        //$this->view->params['layerName'] = LAYERS[self::$appLayer] ?? null;
        //change db
        //$this->layout = '@app/views/layouts/main.php';
        \Yii::$app->redis->executeCommand('select', [self::$appLayer]);
    }

    /**
     * @throws UserException
     * @throws Throwable
     */
    public function run(): string
    {
        $prefix = 'data';
        $n = 1;
        $data = [];
        foreach (ALL_QUEUES as $key => $QUEUE) {
            ${$prefix . $n} = QueueRedis::queueAggregator($QUEUE, $key);
            $data = array_merge($data, ${$prefix . $n});
            $n++;
        }
        $provider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false,
            'sort' => [
                'attributes' => ['name'],
            ],
        ]);

        return QueueManager::widget(['provider' => $provider]);
    }
}
