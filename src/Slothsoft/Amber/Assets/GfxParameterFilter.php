<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

class GfxParameterFilter extends AbstractParameterFilter
{
    const PARAM_GFX_ID = 'gfxId';
    const PARAM_PALETTE_ID = 'paletteId';

    protected function loadMap(): array
    {
        return [
            self::PARAM_INFOSET_ID => 'gfx',
            self::PARAM_GFX_ID => -1,
            self::PARAM_PALETTE_ID => 49,
        ] + parent::loadMap();
    }
}

