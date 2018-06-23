<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;

class EditorBuilder implements ExecutableBuilderStrategyInterface
{

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies
    {
        $game = $args->get(EditorParameterFilter::PARAM_GAME);
        $mod = $args->get(EditorParameterFilter::PARAM_MOD);
        $preset = $args->get(EditorParameterFilter::PARAM_PRESET);
        
        $saveMode = $args->get(EditorParameterFilter::PARAM_SAVE_MODE);
        $saveId = $args->get(EditorParameterFilter::PARAM_SAVE_ID);
        
        $controller = new EditorController();
        $editorConfig = $controller->createEditorConfig($game, $mod, $preset, $saveMode, $saveId);
        
        $loadFile = $args->get(EditorParameterFilter::PARAM_LOAD_FILE);
        $saveFile = $args->get(EditorParameterFilter::PARAM_SAVE_FILE);
        $downloadFile = $args->get(EditorParameterFilter::PARAM_DOWNLOAD_FILE);
        
        $request = (array) $args->get(EditorParameterFilter::PARAM_EDITOR_DATA);
        
        if (isset($request['editor'])) {
            if (isset($request['editor']['archives'])) {
                foreach ($request['editor']['archives'] as $val) {
                    $editorConfig['selectedArchives'][$val] = true;
                }
            }
        }
        if ($loadFile) {
            $editorConfig['selectedArchives'][$loadFile] = true;
        }
        if ($saveFile) {
            $editorConfig['selectedArchives'][$saveFile] = true;
        }
        if ($downloadFile) {
            $editorConfig['selectedArchives'][$downloadFile] = true;
        }
        
        if (isset($_FILES['save'])) {
            foreach ($_FILES['save']['tmp_name'] as $file => $filepath) {
                if (strlen($filepath) and file_exists($filepath)) {
                    $editorConfig['uploadedArchives'][$file] = $filepath;
                }
            }
        }
        
        $editor = $controller->createEditor($editorConfig);
        
        $editor->parseRequest($request);
        
        $resultBuilder = new DOMWriterResultBuilder($editor);
        return new ExecutableStrategies($resultBuilder);
    }
}

