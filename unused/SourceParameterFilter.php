<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class SourceParameterFilter extends AbstractMapParameterFilter
{
    const PARAM_GAME = 'game';
    
    const PARAM_VERSION = 'version';
    
    const PARAM_INFOSET_ID = 'infosetId';
    const PARAM_ARCHIVE_ID = 'archiveId';
    const PARAM_FILE_ID = 'fileId';
    
    const PARAM_USER = 'user';
    
    protected function loadMap(): array
    {
        return [
            self::PARAM_GAME => 'ambermoon',
            self::PARAM_VERSION => 'Thalion-v1.05-DE',
            self::PARAM_INFOSET_ID => 'null',
            self::PARAM_ARCHIVE_ID => '',
            self::PARAM_FILE_ID => '',
            self::PARAM_USER => '',
        ];
    }
}

