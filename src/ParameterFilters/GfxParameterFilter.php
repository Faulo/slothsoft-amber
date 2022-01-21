<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Core\IO\Sanitizer\FileNameSanitizer;
use Slothsoft\Core\IO\Sanitizer\IntegerSanitizer;

class GfxParameterFilter extends ResourceParameterFilter
{
    const PARAM_ARCHIVE_ID = 'archiveId';
    
    const PARAM_FILE_ID = 'fileId';
    
    const PARAM_GFX_ID = 'gfxId';
    
    const PARAM_PALETTE_ID = 'paletteId';
    
    protected function createValueSanitizers(): array
    {
        return [
            self::PARAM_INFOSET_ID => new FileNameSanitizer('gfx'),
            self::PARAM_ARCHIVE_ID => new FileNameSanitizer(''),
            self::PARAM_FILE_ID => new FileNameSanitizer(''),
            self::PARAM_GFX_ID => new IntegerSanitizer(-1),
            self::PARAM_PALETTE_ID => new IntegerSanitizer(49),
        ] + parent::createValueSanitizers();
    }
}

