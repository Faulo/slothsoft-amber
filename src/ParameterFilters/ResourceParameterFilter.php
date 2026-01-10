<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Amber\AmberUser;
use Slothsoft\Core\IO\Sanitizer\FileNameSanitizer;
use Slothsoft\Core\IO\Sanitizer\StringSanitizer;
use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class ResourceParameterFilter extends AbstractMapParameterFilter {
    
    public const PARAM_REPOSITORY = 'repository';
    
    public const PARAM_GAME = 'game';
    
    public const PARAM_VERSION = 'version';
    
    public const PARAM_USER = 'user';
    
    public const PARAM_INFOSET_ID = 'infosetId';
    
    public const PARAM_ARCHIVE_ID = 'archiveId';
    
    public const PARAM_FILE_ID = 'fileId';
    
    protected function createValueSanitizers(): array {
        return [
            self::PARAM_REPOSITORY => new StringSanitizer('farah://slothsoft@amber/source'),
            self::PARAM_GAME => new FileNameSanitizer('ambermoon'),
            self::PARAM_VERSION => new FileNameSanitizer('Thalion-v1.05-DE'),
            self::PARAM_USER => new FileNameSanitizer((string) AmberUser::getId()),
            self::PARAM_INFOSET_ID => new FileNameSanitizer('null'),
            self::PARAM_ARCHIVE_ID => new FileNameSanitizer(''),
            self::PARAM_FILE_ID => new FileNameSanitizer('')
        ];
    }
}

