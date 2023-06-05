<?php

/** Project directory */
define('ABS_PATH', dirname(__DIR__));

require_once ABS_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
\Spatie\Ignition\Ignition::make()
    ->setTheme('dark')
    ->register();

$app = new \Jtech\Framework\App(['project_dir' => ABS_PATH]);

[$request, $response] = $app->start();
exit(
    $app->run($request, $response)
);
