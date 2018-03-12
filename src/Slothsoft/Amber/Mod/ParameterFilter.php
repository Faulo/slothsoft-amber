<?php
namespace Slothsoft\Amber\Mod;

use Slothsoft\Farah\Module\ParameterFilters\MapFilter;

class ParameterFilter extends MapFilter
{
    const PARAM_GAME = 'game';
    const PARAM_MOD = 'mod';
    const PARAM_PRESET = 'preset';
    
    const PARAM_SAVE_MODE = 'SaveDefault';
    const PARAM_SAVE_ID = 'SaveName';
    
    public function __construct(array $map)
    {
        parent::__construct($map + [
            self::PARAM_GAME => 'ambermoon',
            self::PARAM_MOD => 'Thalion-v1.05-DE',
            self::PARAM_PRESET => 'default',
            
            self::PARAM_SAVE_MODE => 'thalion',
            self::PARAM_SAVE_ID => '',
        ]);
    }
}

