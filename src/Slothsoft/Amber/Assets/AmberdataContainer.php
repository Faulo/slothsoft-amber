<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Farah\Module\Node\Asset\AssetInterface;

class AmberdataContainer extends EditorResourceContainerBase
{

    protected function getSelfAssetList(): array
    {
        return [
            'dictionaries',
            'portraits',
            'items',
            'classes',
            'pcs',
            'npcs',
            'monsters',
            'tileset.icons',
            'tileset.labs',
            // 'maps.2d',
            // 'maps.3d',
            // 'worldmap.morag',
            // 'worldmap.kire',
            // 'worldmap.lyramion',
            'graphics'
        ];
    }

    protected function getChildAssetList(): array
    {
        return [
            'dictionaries',
            'portraits',
            'items',
            'classes',
            'pcs',
            'npcs',
            'monsters',
            'tileset.icons',
            'tileset.labs',
            // 'maps.2d',
            // 'maps.3d',
            // 'worldmap.morag',
            // 'worldmap.kire',
            // 'worldmap.lyramion',
            'graphics'
        ];
    }

    protected function inventChildAsset(string $assetName): AssetInterface
    {
        $element = $this->getElement()->withAttributes([
            'name' => $assetName,
            'class' => Amberdata::class
        ]);
        return $this->createChildNode($element);
    }
}
