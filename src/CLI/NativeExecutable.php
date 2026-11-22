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
        return $path !== null && self::isRunnable($path);
    }
    
    public static function requirePath(string $name): string {
        $path = self::getPath($name);
        if ($path === null || ! self::isRunnable($path)) {
            throw new RuntimeException("No supported $name executable is available for " . PHP_OS_FAMILY . ' ' . php_uname('m'));
        }
        return $path;
    }
    
    private static function isRunnable(string $path): bool {
        return is_file($path) && (PHP_OS_FAMILY === 'Windows' || is_executable($path));
    }
    
    /** @param non-empty-list<string> $command */
    public static function execute(string $name, array $command): string {
        $stdout = tmpfile();
        if ($stdout === false) {
            throw new RuntimeException("Could not create the $name output stream.");
        }
        
        $stderr = tmpfile();
        if ($stderr === false) {
            fclose($stdout);
            throw new RuntimeException("Could not create the $name error stream.");
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
        
        if ($output === false || $errorOutput === false) {
            throw new RuntimeException("Could not read $name output streams.");
        }
        
        if ($exitCode !== 0) {
            throw new RuntimeException("$name failed with exit code $exitCode!" . PHP_EOL . '> ' . self::formatCommand($command) . PHP_EOL . $errorOutput . PHP_EOL . $output);
        }
        return $output;
    }
    
    /** @param non-empty-list<string> $command */
    private static function formatCommand(array $command): string {
        return implode(' ', array_map('escapeshellarg', $command));
    }
}
