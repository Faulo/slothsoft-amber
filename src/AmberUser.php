<?php
declare(strict_types = 1);
namespace Slothsoft\Amber;

class AmberUser {
    
    /**
     *
     * @todo put this somewhere else maybe
     * @return string
     */
    public static function getId(): string {
        static $instance;
        if ($instance === null) {
            $instance = uniqid();
            $instance = '';
        }
        return $instance;
    }
}

