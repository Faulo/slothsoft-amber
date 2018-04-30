<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\Executables\AmberExecutableCreator;
use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Node\Asset\AssetBase;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;

class Amberdata extends AssetBase
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
}
