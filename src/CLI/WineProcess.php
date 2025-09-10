<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Core\FileSystem;
use Slothsoft\Core\ServerEnvironment;
use Symfony\Component\Process\Process;

class WineProcess extends Process {

    private const SERVER_DIRECTORY = 'wine';

    private const SERVER_TIMEOUT = 60;

    private static function getDirectoryPrefix(): string {
        $directory = ServerEnvironment::getDataDirectory() . DIRECTORY_SEPARATOR . self::SERVER_DIRECTORY;
        FileSystem::ensureDirectory($directory);
        return 'WINEPREFIX=' . escapeshellarg($directory);
    }

    private static function startServer(): string {
        $prefix = self::getDirectoryPrefix();
        $server = new Process([
            $prefix,
            'wineserver',
            '-p',
            self::SERVER_TIMEOUT
        ]);
        $server->run();
        return $prefix;
    }

    public function __construct(array $command) {
        if (PHP_OS_FAMILY !== 'Windows') {
            $prefix = self::startServer();

            array_unshift($command, $prefix);
            array_unshift($command, 'wine');
        }

        parent::__construct($command);
    }
}