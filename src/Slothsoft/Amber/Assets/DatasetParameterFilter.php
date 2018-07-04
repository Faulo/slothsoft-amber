<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

class DatasetParameterFilter extends AbstractParameterFilter
{
    protected function loadMap(): array
    {
        return [
            self::PARAM_GAME => 'ambermoon',
            self::PARAM_VERSION => 'Thalion-v1.05-DE',
            self::PARAM_INFOSET_ID => 'null',
            self::PARAM_USER => '',
        ];
    }
}

