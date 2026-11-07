<?php
declare(strict_types = 1);

namespace Slothsoft\Amber;

final class AmberUser {
    
    public const DEFAULT_ID = 'default-user';
    
    /**
     *
     * @return string
     * @todo put this somewhere else maybe
     */
    public static function getNewIdIfDefault(string $previous): string {
        if ($previous !== self::DEFAULT_ID) {
            return $previous;
        }
        static $instance;
        if ($instance === null) {
            $instance = uniqid();
        }
        return $instance;
    }
}

