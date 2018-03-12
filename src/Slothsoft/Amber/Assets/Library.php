<?php
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Node\Asset\AssetImplementation;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;
use Slothsoft\Farah\Module\Results\DOMWriterResult;
use Slothsoft\Farah\Module\Results\ResultInterface;

class Library extends AssetImplementation
{

    protected function loadParameterFilter(): ParameterFilterInterface
    {
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
}
