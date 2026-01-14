<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Amber\AmberUser;
use Slothsoft\Core\IO\Sanitizer\FileNameSanitizer;
use Slothsoft\Core\IO\Sanitizer\StringSanitizer;
use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class UserParameterFilter extends AbstractMapParameterFilter {
    
    public const PARAM_REPOSITORY = 'repository';
    
    public const PARAM_GAME = 'game';
    
    public const PARAM_VERSION = 'version';
    
    public const PARAM_USER = 'user';
    
    protected function createValueSanitizers(): array {
        return [
            self::PARAM_REPOSITORY => new StringSanitizer('farah://slothsoft@amber/source'),
            self::PARAM_GAME => new FileNameSanitizer('ambermoon'),
            self::PARAM_VERSION => new FileNameSanitizer('Thalion-v1.05-DE'),
            self::PARAM_USER => new FileNameSanitizer((string) AmberUser::getId())
        ];
    }
}

