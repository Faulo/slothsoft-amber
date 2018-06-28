<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\NullResultBuilder;

class StylesheetBuilder implements ExecutableBuilderStrategyInterface
{

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies
    {
        $game = $args->get(StylesheetParameterFilter::PARAM_GAME);
        $version = $args->get(StylesheetParameterFilter::PARAM_VERSION);
        $infoset = $args->get(StylesheetParameterFilter::PARAM_ID);
        $user = $args->get(StylesheetParameterFilter::PARAM_USER);
        
        $controller = new EditorController();
        
        $editorConfig = $controller->createEditorConfig($game, $version, $infoset, $user);
        
        $editor = $controller->createEditor($editorConfig);
        
        $resultBuilder = new NullResultBuilder();
        return new ExecutableStrategies($resultBuilder);
    }
}

