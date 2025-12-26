<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Core\IO\Sanitizer\ArraySanitizer;
use Slothsoft\Core\IO\Sanitizer\FileNameSanitizer;

final class EditorParameterFilter extends ResourceParameterFilter {
    
    public const PARAM_EDITOR_DATA = 'save';
    
    protected function createValueSanitizers(): array {
        return [
            self::PARAM_INFOSET_ID => new FileNameSanitizer('null'),
            self::PARAM_EDITOR_DATA => new ArraySanitizer()
        ] + parent::createValueSanitizers();
    }
}

