<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ResourceAssets;

class GfxParameterFilter extends AbstractParameterFilter
{

    const PARAM_GFX_ID = 'gfxId';

    protected function loadMap(): array
    {
        return parent::loadMap() + [
            self::PARAM_GFX_ID => 0
        ];
    }
}

