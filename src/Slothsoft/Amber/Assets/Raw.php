<?php
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Results\FileWriterResult;
use Slothsoft\Farah\Module\Results\ResultInterface;
use Slothsoft\Savegame\Editor;
use Slothsoft\Savegame\Build\XmlBuilder;

class Raw extends EditorResourceAsset
{

    protected function loadResult(FarahUrl $url): ResultInterface
    {
        $args = $url->getArguments();
        
        $args->set(ParameterFilter::PARAM_PRESET, $this->getName());
        
        $controller = new EditorController();
        
        $editorConfig = $controller->createEditorConfig($args);
        
        $editor = $controller->createEditor($editorConfig);
        
        return new FileWriterResult($url, $this->createEditorFile($editor));
    }

    private function createEditorFile(Editor $editor): HTTPFile
    {
        $stream = $this->createEditorStream($editor);
        
        $file = HTTPFile::createFromStream($stream, $this->getName() . '.xml');
        
        fclose($stream);
        
        return $file;
    }

    private function createEditorStream(Editor $editor)
    {
        $builder = new XmlBuilder();
        $builder->registerTagBlacklist([
            'archive'
        ]);
        $builder->registerAttributeBlacklist([
            'value-id',
            'position',
            'min',
            'max',
            'bit',
            'encoding'
        ]);
        return $builder->buildStream($editor->getSavegame());
    }
}

