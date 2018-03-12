<?php
namespace Slothsoft\Amber\Assets;


use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Node\Asset\AssetImplementation;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;
use Slothsoft\Farah\Module\PathResolvers\PathResolverCatalog;
use Slothsoft\Farah\Module\PathResolvers\PathResolverInterface;
use Slothsoft\Farah\Module\Results\DOMWriterResult;
use Slothsoft\Farah\Module\Results\ResultInterface;

class LibraryContainer extends AssetImplementation
{
    private $selfAssetList = [
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
        'graphics',
    ];
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
        'graphics',
    ];
    
    protected function loadParameterFilter() : ParameterFilterInterface{
        return new ParameterFilter([]);
    }
    
    protected function loadResult(FarahUrl $url): ResultInterface
    {
        my_dump($this->getElementAttribute('path'));
        my_dump($url);
        $args = $url->getArguments();
        
        $controller = new EditorController();
        
        $editorConfig = $controller->createEditorConfig($args);
        
        $editor = $controller->createEditor($editorConfig);
        
        return new DOMWriterResult($url, $editor);
    }
    
    protected function loadPathResolver() : PathResolverInterface {
        $map = ['/' => $this];
        foreach ($this->childAssetList as $assetName) {
            $element = $this->getElement()->withAttributes(['name' => $assetName, 'class' => Library::class]);
            $map["/$assetName"] = $this->createChildNode($element);
        }
        return PathResolverCatalog::createMapPathResolver($this, $map);
    }
}
