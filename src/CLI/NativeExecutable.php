<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use RuntimeException;

final class NativeExecutable {

    public static function getPath(string $name): ?string {
        if (! in_array($name, ['ambtool', 'amgfx'], true)) {
            return null;
        }

        $architecture = strtolower(php_uname('m'));
        switch ($architecture) {
            case 'x86_64':
            case 'amd64':
                $architecture = 'x64';
                break;
            case 'aarch64':
            case 'arm64':
                $architecture = 'arm64';
                break;
            default:
                return null;
        }

        switch (PHP_OS_FAMILY) {
            case 'Windows':
                if ($architecture !== 'x64') {
                    return null;
                }
                $extension = 'exe';
                break;
            case 'Linux':
                $extension = $architecture;
                break;
            default:
                return null;
        }

        return dirname(__DIR__, 2) . '/assets/cli/' . $name . '/' . $name . '.' . $extension;
    }

    public static function isSupported(string $name): bool {
        $path = self::getPath($name);
        return $path !== null and is_file($path) and (PHP_OS_FAMILY === 'Windows' or is_executable($path));
    }

    public static function requirePath(string $name): string {
        if (! self::isSupported($name)) {
            throw new RuntimeException("No supported $name executable is available for " . PHP_OS_FAMILY . ' ' . php_uname('m'));
        }
        return self::getPath($name);
    }
    
    public static function execute(string $name, array $command): string {
        $stdout = tmpfile();
        $stderr = tmpfile();
        if ($stdout === false or $stderr === false) {
            self::closeStream($stdout);
            self::closeStream($stderr);
            throw new RuntimeException("Could not create $name output streams.");
        }
        
        $process = @proc_open($command, [
            0 => ['pipe', 'r'],
            1 => $stdout,
            2 => $stderr
        ], $pipes);
        if (! is_resource($process)) {
            fclose($stdout);
            fclose($stderr);
            throw new RuntimeException("Could not start $name.");
        }
        fclose($pipes[0]);
        
        $exitCode = proc_close($process);
        rewind($stdout);
        rewind($stderr);
        $output = stream_get_contents($stdout);
        $errorOutput = stream_get_contents($stderr);
        fclose($stdout);
        fclose($stderr);
        
        if ($exitCode !== 0) {
            throw new RuntimeException("$name failed with exit code $exitCode!" . PHP_EOL . '> ' . self::formatCommand($command) . PHP_EOL . $errorOutput . PHP_EOL . $output);
        }
        return $output;
    }
    
    private static function closeStream($stream): void {
        if (is_resource($stream)) {
            fclose($stream);
        }
    }
    
    private static function formatCommand(array $command): string {
        return implode(' ', array_map('escapeshellarg', $command));
    }
}
