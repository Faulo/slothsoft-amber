<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Core\IO\Sanitizer\ArraySanitizer;
use Slothsoft\Core\IO\Sanitizer\StringSanitizer;

final class EditorParameterFilter extends InfosetParameterFilter {
    
    public const PARAM_ARCHIVE_ID = 'archivePath';
    
    public const PARAM_EDITOR_ACTION = 'action';
    
    public const PARAM_EDITOR_UPLOAD = 'upload';
    
    public const PARAM_EDITOR_DOWNLOAD = 'download';
    
    public const PARAM_EDITOR_DATA = 'save';
    
    public const PARAM_EDITOR_ACTION_VIEW = 'view';
    
    public const PARAM_EDITOR_ACTION_SAVE = 'save';
    
    public const PARAM_EDITOR_ACTION_UPLOAD = 'upload';
    
    protected function createValueSanitizers(): array {
        return [
            self::PARAM_ARCHIVE_ID => new StringSanitizer(''),
            self::PARAM_EDITOR_ACTION => new StringSanitizer(self::PARAM_EDITOR_ACTION_VIEW),
            self::PARAM_EDITOR_UPLOAD => new ArraySanitizer(),
            self::PARAM_EDITOR_DOWNLOAD => new StringSanitizer(''),
            self::PARAM_EDITOR_DATA => new ArraySanitizer()
        ] + parent::createValueSanitizers();
    }
}

