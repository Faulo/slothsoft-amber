<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\Executables\AmberExecutableCreator;
use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Node\Asset\AssetBase;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;
use Slothsoft\Savegame\Editor;
use Slothsoft\Savegame\Build\XmlBuilder;

class Raw extends AssetBase
{

    protected function loadParameterFilter(): ParameterFilterInterface
    {
        return new ParameterFilter([]);
    }

    protected function loadExecutable(FarahUrlArguments $args): ExecutableInterface
    {
        $args->set(ParameterFilter::PARAM_PRESET, $this->getName());
        
        $controller = new EditorController();
        
        $editorConfig = $controller->createEditorConfig($args);
        
        $editor = $controller->createEditor($editorConfig);
        
        $creator = new AmberExecutableCreator($this, $args);
        return $creator->createEditorExecutable($editor);
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

