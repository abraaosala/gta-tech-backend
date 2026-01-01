<?php

namespace app\classes;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

class Logger
{
    private static $logger;

    public static function get(): MonologLogger
    {
        if (self::$logger === null) {
            self::$logger = new MonologLogger('app');

            // Garante que o diretório de logs existe
            $logPath = dirname(__DIR__, 2) . '/storage/logs/app.log';
            if (!is_dir(dirname($logPath))) {
                mkdir(dirname($logPath), 0777, true);
            }

            self::$logger->pushHandler(new StreamHandler($logPath, Level::Debug));
        }

        return self::$logger;
    }

    public static function error(string $message, array $context = []): void
    {
        self::get()->error($message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::get()->info($message, $context);
    }
}
