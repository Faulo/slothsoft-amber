<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Core\IO\Sanitizer\ArraySanitizer;

final class EditorParameterFilter extends InfosetParameterFilter {
    
    public const PARAM_EDITOR_DATA = 'save';
    
    public const PARAM_EDITOR_DATA_ARCHIVE = 'archiveId';
    
    protected function createValueSanitizers(): array {
        return [
            self::PARAM_EDITOR_DATA => new ArraySanitizer()
        ] + parent::createValueSanitizers();
    }
}

