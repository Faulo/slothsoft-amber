<?php
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\PathResolvers\PathResolverCatalog;
use Slothsoft\Farah\Module\PathResolvers\PathResolverInterface;
use Slothsoft\Farah\Module\Results\DOMWriterResult;
use Slothsoft\Farah\Module\Results\ResultInterface;

class Raw extends EditorResourceAsset
{
    protected function loadResult(FarahUrl $url): ResultInterface
    {
        my_dump($url);
        $args = $url->getArguments();
        
        $controller = new EditorController();
        
        $editorConfig = $controller->createEditorConfig($args);
        
        $editor = $controller->createEditor($editorConfig);
        
        return new DOMWriterResult($url, $editor);
    }
    
    protected function loadPathResolver() : PathResolverInterface {
        return PathResolverCatalog::createCatchAllPathResolver($this);
    }
}

