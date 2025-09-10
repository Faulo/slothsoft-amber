<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Core\FileSystem;
use Slothsoft\Core\ServerEnvironment;
use Symfony\Component\Process\Process;

class WineProcess extends Process {

    private const SERVER_PREFIX = 'WINEPREFIX';

    private const SERVER_DIRECTORY = 'wine';

    private const SERVER_TIMEOUT = 60;

    private const SERVER_COMMAND = [
        'wineserver',
        '-p',
        self::SERVER_TIMEOUT
    ];

    private static function getDirectoryPrefix(): array {
        $directory = ServerEnvironment::getDataDirectory() . DIRECTORY_SEPARATOR . self::SERVER_DIRECTORY;
        FileSystem::ensureDirectory($directory);
        return [
            self::SERVER_PREFIX => $directory
        ];
    }

    private static function startServer(): array {
        $env = self::getDirectoryPrefix();
        $server = new Process(self::SERVER_COMMAND, null, $env);
        $server->run();
        return $env;
    }

    public function __construct(array $command) {
        if (PHP_OS_FAMILY !== 'Windows') {
            $env = self::startServer();

            array_unshift($command, 'wine');
        }

        parent::__construct($command, null, $env);
    }
}