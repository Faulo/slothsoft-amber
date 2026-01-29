<?php
declare(strict_types = 1);
namespace Slothsoft\Amber;

use Slothsoft\Farah\FarahUrl\FarahUrl;

final class AmberUtils {
    
    public static function toHref(FarahUrl $url): string {
        return substr((string) $url, strlen('farah:/'));
    }
}

