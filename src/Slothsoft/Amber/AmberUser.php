<?php
namespace Slothsoft\Amber;

class AmberUser
{
    /**
     * @TODO    put this somewhere elese maybe
     * @return string
     */
    public static function getId() : string {
        static $instance;
        if ($instance === null) {
            $instance = uniqid();
        }
        return $instance;
    }
}

