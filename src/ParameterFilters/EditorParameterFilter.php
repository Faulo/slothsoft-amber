<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Core\IO\Sanitizer\ArraySanitizer;

final class EditorParameterFilter extends InfosetParameterFilter {
    
    public const PARAM_EDITOR_DATA = 'save';
    
    public const PARAM_EDITOR_DATA_ARCHIVE = 'archivePath';
    
    public const PARAM_EDITOR_DATA_VALUES = 'data';
    
    public const PARAM_EDITOR_DATA_ACTION = 'action';
    
    public const PARAM_EDITOR_ACTION_VIEW = 'view';
    
    public const PARAM_EDITOR_ACTION_SAVE = 'save';
    
    public const PARAM_EDITOR_ACTION_DOWNLOAD = 'download';
    
    public const PARAM_EDITOR_ACTION_UPLOAD = 'upload';
    
    protected function createValueSanitizers(): array {
        return [
            self::PARAM_EDITOR_DATA => new ArraySanitizer()
        ] + parent::createValueSanitizers();
    }
}

