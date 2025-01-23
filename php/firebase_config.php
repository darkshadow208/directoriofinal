<?php

require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

$firebase = (new Factory)
    ->withServiceAccount(__DIR__ . '/proyectopca-c251b-firebase-adminsdk-fbsvc-21e89c8600.json') // Ruta a tu archivo JSON
    ->create();

$storage = $firebase->createStorage();

return $storage;
