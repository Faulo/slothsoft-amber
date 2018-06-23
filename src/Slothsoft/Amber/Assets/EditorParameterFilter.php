<?php
namespace Slothsoft\Amber\Assets;

use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class EditorParameterFilter extends AbstractMapParameterFilter
{

    const PARAM_GAME = 'game';

    const PARAM_MOD = 'mod';

    const PARAM_PRESET = 'preset';

    const PARAM_SAVE_MODE = 'SaveDefault';

    const PARAM_SAVE_ID = 'SaveName';

    const PARAM_LOAD_FILE = 'LoadFile';

    const PARAM_SAVE_FILE = 'SaveFile';

    const PARAM_DOWNLOAD_FILE = 'DownloadFile';

    const PARAM_EDITOR_DATA = 'save';

    protected function loadMap(): array
    {
        return [
            self::PARAM_GAME => 'ambermoon',
            self::PARAM_MOD => 'Thalion-v1.05-DE',
            self::PARAM_PRESET => 'default',
            
            self::PARAM_SAVE_MODE => 'thalion',
            self::PARAM_SAVE_ID => '',
            
            self::PARAM_LOAD_FILE => '',
            self::PARAM_SAVE_FILE => '',
            self::PARAM_DOWNLOAD_FILE => '',
            
            self::PARAM_EDITOR_DATA => []
        ];
    }
}

