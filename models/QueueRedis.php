<?php

namespace elmys\yii2\queueRedis\models;
use yii\base\Model;
use yii\base\UserException;
use yii\queue\redis\Queue;

class QueueRedis extends Model
{
    /**
     * @throws UserException
     */
    static public function queueAggregator(array $inputQueue, $group = 'group one'): array
    {
        $data = [];
        foreach ($inputQueue as $queue) {
            $currentQueue = \Yii::$app->{$queue};
            if (!$currentQueue instanceof Queue) {
                throw new UserException(\Yii::t('queueRedis', 'Queue {queue} is not instance of Redis'), ['queue' => $queue]);
            }
            $prefix = $currentQueue->channel;
            $waiting = $currentQueue->redis->llen("$prefix.waiting");
            $delayed = $currentQueue->redis->zcount("$prefix.delayed", '-inf', '+inf');
            $reserved = $currentQueue->redis->zcount("$prefix.reserved", '-inf', '+inf');
            $total = $currentQueue->redis->get("$prefix.message_id") ?? 0;
            $data [] = [
                'name' => $queue,
                'group' => $group,
                'waiting' => $waiting,
                'delayed' => $delayed,
                'reserved' => $reserved,
                'total' => $total,
            ];
        }
        sort($data);
        return $data;
    }
}
