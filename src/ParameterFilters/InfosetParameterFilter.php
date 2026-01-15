<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Core\IO\Sanitizer\FileNameSanitizer;

class InfosetParameterFilter extends UserParameterFilter {
    
    public const PARAM_INFOSET_ID = 'infosetId';
    
    protected function createValueSanitizers(): array {
        return [
            self::PARAM_INFOSET_ID => new FileNameSanitizer('null')
        ] + parent::createValueSanitizers();
    }
}

