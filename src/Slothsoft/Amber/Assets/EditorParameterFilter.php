<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

class EditorParameterFilter extends AbstractParameterFilter
{
    const PARAM_EDITOR_DATA = 'save';
    
    protected function loadMap(): array
    {
        return [
            self::PARAM_GAME => 'ambermoon',
            self::PARAM_VERSION => 'Thalion-v1.05-DE',
            self::PARAM_INFOSET_ID => 'null',
            self::PARAM_USER => '',
            
            self::PARAM_EDITOR_DATA => [],
        ];
    }
}

