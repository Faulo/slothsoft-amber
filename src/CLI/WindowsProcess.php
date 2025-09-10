<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Core\FileSystem;
use Slothsoft\Core\ServerEnvironment;
use Symfony\Component\Process\Process;

class WindowsProcess {

    public static function create(array $command): Process {
        switch (PHP_OS_FAMILY) {
            case 'Windows':
                $env = self::startWineServer();

                array_unshift($command, 'wine');

                return new Process($command, null, $env);
            default:
                return new Process($command);
        }
    }

    private const WINE_PREFIX = 'WINEPREFIX';

    private const WINE_DIRECTORY = 'wine';

    private const WINE_TIMEOUT = 60;

    private const WINE_COMMAND = [
        'wineserver',
        '-p',
        self::WINE_TIMEOUT
    ];

    private static function startWineServer(): array {
        $env = self::getWineDirectory();
        $server = new Process(self::WINE_COMMAND, null, $env);
        $server->run();

        if ($server->getExitCode() !== 0) {
            trigger_error("wineserver failed!" . PHP_EOL . '> ' . $server->getCommandLine() . PHP_EOL . $server->getErrorOutput() . PHP_EOL . $server->getOutput(), E_USER_ERROR);
        }

        return $env;
    }

    private static function getWineDirectory(): array {
        $directory = ServerEnvironment::getDataDirectory() . DIRECTORY_SEPARATOR . self::WINE_DIRECTORY;
        FileSystem::ensureDirectory($directory);
        return [
            self::WINE_PREFIX => $directory
        ];
    }
}