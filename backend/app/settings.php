<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            $isProduction = (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production');
            return new Settings([
                'displayErrorDetails' => !$isProduction,
                'logError'            => $isProduction ? true : false,
                'logErrorDetails'     => $isProduction ? false : true,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
            ]);
        }
    ]);
};
