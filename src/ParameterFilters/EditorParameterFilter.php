<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Core\IO\Sanitizer\ArraySanitizer;

class EditorParameterFilter extends ResourceParameterFilter {
    
    const PARAM_EDITOR_DATA = 'save';
    
    protected function createValueSanitizers(): array {
        return [
            self::PARAM_EDITOR_DATA => new ArraySanitizer()
        ] + parent::createValueSanitizers();
    }
}

