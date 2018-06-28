<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\NullResultBuilder;

class GfxBuilder implements ExecutableBuilderStrategyInterface
{

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies
    {
        $game = $args->get(GfxParameterFilter::PARAM_GAME);
        $version = $args->get(GfxParameterFilter::PARAM_VERSION);
        $id = $args->get(GfxParameterFilter::PARAM_ID);
        $user = $args->get(GfxParameterFilter::PARAM_USER);
        
        $controller = new EditorController();
        
        if ($id === '') {
            $resultBuilder = new NullResultBuilder();
        } else {
            $editorConfig = $controller->createEditorConfig($game, $version, $id, $user);
            $editor = $controller->createEditor($editorConfig);
            
            $gfxFile = $editor->findGameFile($id);
            $ambGfx = $controller->createAmbGfx();
            
            $tgaFile = temp_file(__NAMESPACE__);
            $res = $ambGfx->extractTga((string) $gfxFile, $tgaFile);
            var_dump($res);
        }
        
        return new ExecutableStrategies($resultBuilder);
    }
}

