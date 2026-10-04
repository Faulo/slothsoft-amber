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
}
