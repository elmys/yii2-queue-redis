<?php

namespace elmys\yii2\queueRedis\actions;

use Throwable;
use Yii;
use yii\base\Action;
use yii\base\UserException;

class QueueRedisCleanAction extends Action
{
    /**
     * @throws UserException
     * @throws Throwable
     */
    public function run(): \yii\web\Response|\yii\console\Response
    {
        $get = Yii::$app->request->get();
        $qid = $get['qid'];
        $layer = $get['layer'];
        Yii::$app->redis->executeCommand('select', [$layer]);
        try {
            $currentQueue = \Yii::$app->{$qid};
            $currentQueue->clear();
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', \Yii::t('queueRedis', 'Failed to clear queue') . $e->getMessage() . $qid);
        }
        return Yii::$app->response->redirect(\Yii::$app->request->referrer);
    }
}
