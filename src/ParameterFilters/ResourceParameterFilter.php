<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Core\IO\Sanitizer\FileNameSanitizer;

class ResourceParameterFilter extends UserParameterFilter {
    
    public const PARAM_INFOSET_ID = 'infosetId';
    
    public const PARAM_ARCHIVE_ID = 'archiveId';
    
    public const PARAM_FILE_ID = 'fileId';
    
    protected function createValueSanitizers(): array {
        return [
            self::PARAM_INFOSET_ID => new FileNameSanitizer('null'),
            self::PARAM_ARCHIVE_ID => new FileNameSanitizer(''),
            self::PARAM_FILE_ID => new FileNameSanitizer('')
        ] + parent::createValueSanitizers();
    }
}

