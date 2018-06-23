<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Farah\Module\Node\Asset\AssetInterface;
use Slothsoft\Farah\Module\Node\Enhancements\AssetBuilderTrait;

class AmberdataContainer extends EditorResourceContainerBase
{
    use AssetBuilderTrait;

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
        $data = [];
        $data[] = "/editor/resource/raw/$assetName";
        if ($assetName !== 'dictionaries') {
            $data[] = "/editor/resource/amberdata/dictionaries";
        }
        return $this->buildFragment($assetName, $data, "/games/ambermoon/template.extract");
    }
}
