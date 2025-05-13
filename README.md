# yii2-queue-redis
Quick deployment of an interface for easy work with queues based on Redis for Yii2

Installation
-
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

* Either run

```
php composer.phar require --prefer-dist "elmys/yii2-queue-redis" : "~1.0"
```

or add

```
"elmys/yii2-queue-redis" : "~1.0"
```

to the require section of your application's `composer.json` file.

Usage
-

1. Add to your index-file new import ```require __DIR__ . '/../config/definitions.php';``` with bellow content:
```php
const QUEUE_ACCOUNT = 'queueAccount'; // Here will be real names of your queue on redis created
const QUEUE_BILLING = 'queueBilling';
const QUEUE_OTHER = 'queueOther';

const LAYER_DEV = '0';
const LAYER_STAGE = '1';
const LAYER_PROD = '9';

const LAYERS = [
    LAYER_DEV => 'dev',
    LAYER_STAGE => 'stage',
    LAYER_PROD => 'prod',
];

const ONE_QUEUES = [
    QUEUE_ACCOUNT,
    QUEUE_BILLING,
];
const TWO_QUEUES = [
    QUEUE_OTHER,
];
const ALL_QUEUES = [
    'one-micro-service' => ONE_QUEUES, // these array keys for CSS-styling only
    'two-micro-service' =>  TWO_QUEUES,    
];
```
Define layers for yii-environment. It can be used for switch DB on redis, just adding `?layer=0` to url. You can write any digits from 0 to 9;
Separated constants with queue names need for stylizing with css and visual usefully.

2. Add bellow files and import it to main config on import block `web.php`:

```php
$redis = require __DIR__ . '/redis.php';
$queuesBootstrap = require __DIR__ . '/queues-bootstrap.php';
$queues = require __DIR__ . '/queues-local.php';
```

- redis.php
```php
<?php
return [
    'class' => \yii\redis\Connection::class,
    'retries' => 1,
    'hostname' => 'your_hostname',
    'port' => 6379,
    'database' => 0,
    'password' => 'your_password',
];
```

- queues-bootstrap.php
```php
<?php
return array_merge(ONE_QUEUES, TWO_QUEUES);
```

- queues-local.php
```php
<?php
use yii\queue\redis\Queue;
$redisComponent = 'redis';
$queues = require __DIR__ . '/queues-bootstrap.php';
$res = [];
foreach ($queues as $queue) {
    $res[$queue]= [
        'class' => Queue::class,
        'redis' => $redisComponent,
        'channel' => $queue,
    ];
}

return $res;
```
and edit here:
```php
$config = [
    ...
    'bootstrap' => array_merge(['log'], $queuesBootstrap),
    'components' => [
            'queues' => $queues,
            ...
            'i18n' => [
                        'translations' => [
                            'queueRedis*' => [
                                'class' => 'yii\i18n\PhpMessageSource',
                                'basePath' => '@vendor/elmys/yii2-queue-redis/messages',
                            ],
                        ],
                    ],
    ...
```

3. Current layer number will be saved on cookies. For correct init, override your init-method on AppAsset:
```php
public function init()
    {
        elmys\yii2\queueRedis\actions\QueueRedisAction::staticInit();
        parent::init();
    }
```
4. In your layout you can use current layer-variable, for example:
```php
$layerName = LAYERS[elmys\yii2\queueRedis\actions\QueueRedisAction::$appLayer] ?? null;
```

Also, edit your site controller:
```php
public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'elmys\yii2\queueRedis\actions\QueueRedisAction',
            ],
            'queue-clear' => [
                'class' => 'elmys\yii2\queueRedis\actions\QueueRedisCleanAction',
            ],
            ...
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ]);
    }
```
 
5. Add and customize your css:
```css
/* queueRedis */

.app-layer {
    --bs-bg-opacity: 1;
}

.app-layer.dev {
    background-color: rgba(25, 135, 84, 1) !important;
}

.app-layer.stage {
    background-color: rgba(13, 202, 240, 1) !important;
}

.app-layer.prod {
    background-color: rgba(33, 37, 41, 1) !important;
}

.admin-queues .badge{
    background-color: #999999;
}
.admin-queues .one-micro-service .badge.filled{
    background-color: #409600;
}
.admin-queues .two-micro-service .badge.filled{
    background-color: #FED74A;
    color: #000000;
}
```

6. Done.