<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

class EditorGfxParameterFilter extends EditorParameterFilter
{

    const PARAM_GFX_ID = 'gfxId';

    protected function loadMap() : array {
        return parent::loadMap() + [
            self::PARAM_GFX_ID => 0,
        ];
    }
}

