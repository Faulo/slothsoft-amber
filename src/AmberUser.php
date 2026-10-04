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
        static $instance;
        if ($instance === null) {
            $instance = $previous === self::DEFAULT_ID ? uniqid() : $previous;
        }
        return $instance;
    }
}

