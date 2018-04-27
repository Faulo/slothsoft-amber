<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Farah\Module\Node\Asset\AssetBase;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;
use Slothsoft\Farah\Module\PathResolvers\PathResolverCatalog;
use Slothsoft\Farah\Module\PathResolvers\PathResolverInterface;

class RawContainer extends AssetBase
{
    

    private $childAssetList = [
        'dictionaries',
        'portraits',
        'items',
        'classes',
        'pcs',
        'npcs',
        'monsters',
        'tileset.icons',
        'tileset.labs',
        'maps.2d',
        'maps.3d',
        'worldmap.morag',
        'worldmap.kire',
        'worldmap.lyramion',
        'graphics'
    ];
    
    protected function loadParameterFilter(): ParameterFilterInterface
    {
        return new ParameterFilter([]);
    }

    protected function loadPathResolver(): PathResolverInterface
    {
        $map = [
            '/' => $this
        ];
        foreach ($this->childAssetList as $assetName) {
            $element = $this->getElement()->withAttributes([
                'name' => $assetName,
                'class' => Raw::class
            ]);
            $map["/$assetName"] = $this->createChildNode($element);
        }
        return PathResolverCatalog::createMapPathResolver($this, $map);
    }
}

