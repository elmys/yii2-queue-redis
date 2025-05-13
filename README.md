# yii2-queue-redis
Quick deployment of an interface for easy work with queues based on Redis for Yii2

Installation
-
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

* Either run

```
php composer.phar require --prefer-dist "elmys/yii2-queue-redis" : "master@dev"
```

or add

```
"elmys/yii2-queue-redis" : "master@dev"
```

to the require section of your application's `composer.json` file.

Usage
-
In your model of statuses:
```php
// Import
use elmys\queueRedis;

```

Add and customize your css:
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
.admin-queues .app .badge.filled{
    background-color: #409600;
}
.admin-queues .ps .badge.filled{
    background-color: #FED74A;
    color: #000000;
}
.admin-queues .ds .badge.filled{
    background-color: #900052;
}
.admin-queues .ts .badge.filled{
    background-color: #FF7123;
}
```