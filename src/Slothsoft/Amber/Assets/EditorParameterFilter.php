<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

class EditorParameterFilter extends AbstractParameterFilter
{
    const PARAM_EDITOR_DATA = 'save';
    
    protected function loadMap(): array
    {
        return parent::loadMap() + [
            self::PARAM_EDITOR_DATA => [],
        ];
    }
}

