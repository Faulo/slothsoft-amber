<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\Controller\EditorParameters;
use Slothsoft\Amber\ParameterFilters\EditorParameterFilter;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Farah\Exception\HttpDownloadAssetException;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ChunkWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;
use Slothsoft\Savegame\Build\XmlBuilder;

final class EditorDataBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $repository = $args->get(EditorParameterFilter::PARAM_REPOSITORY);
        $game = $args->get(EditorParameterFilter::PARAM_GAME);
        $version = $args->get(EditorParameterFilter::PARAM_VERSION);
        $user = $args->get(EditorParameterFilter::PARAM_USER);
        $infosetId = $args->get(EditorParameterFilter::PARAM_INFOSET_ID);
        $request = $args->get(EditorParameterFilter::PARAM_EDITOR_DATA);
        
        $parameters = new EditorParameters($repository, $game, $version, $user, $infosetId, $request);
        
        $controller = new EditorController();
        $config = $controller->createEditorConfig($parameters);
        $editor = $controller->createEditor($config);
        
        $action = $request[EditorParameterFilter::PARAM_EDITOR_DATA_ACTION] ?? EditorParameterFilter::PARAM_EDITOR_ACTION_VIEW;
        
        if ($action === EditorParameterFilter::PARAM_EDITOR_ACTION_UPLOAD) {
            $errors = $_FILES[EditorParameterFilter::PARAM_EDITOR_DATA]['error'] ?? [];
            foreach ($errors as $archivePath => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $path = $_FILES[EditorParameterFilter::PARAM_EDITOR_DATA]['tmp_name'][$archivePath] ?? '';
                    $editor->writeGameFile($archivePath, FileInfoFactory::createFromUpload($path));
                }
            }
        }
        
        $savegame = $editor->getSavegameNode();
        $cacheDirectory = $config->cacheDirectory . DIRECTORY_SEPARATOR . 'editor-data';
        
        $writer = new XmlBuilder($savegame);
        
        if (isset($request[EditorParameterFilter::PARAM_EDITOR_DATA_ARCHIVE])) {
            $archivePath = $request[EditorParameterFilter::PARAM_EDITOR_DATA_ARCHIVE];
            $archive = $editor->loadArchive($archivePath, true);
            
            $writer->setCacheDirectory($cacheDirectory . DIRECTORY_SEPARATOR . $archivePath);
            
            if (isset($request[EditorParameterFilter::PARAM_EDITOR_DATA_VALUES])) {
                $editor->applyValues($request[EditorParameterFilter::PARAM_EDITOR_DATA_VALUES]);
            }
            
            if ($action === EditorParameterFilter::PARAM_EDITOR_ACTION_SAVE) {
                $editor->writeGameFile($archivePath, $archive);
            }
            
            if ($action === EditorParameterFilter::PARAM_EDITOR_ACTION_DOWNLOAD) {
                $resultBuilder = new FileWriterResultBuilder($archive, $archive->getName());
                $strategies = new ExecutableStrategies($resultBuilder);
                throw new HttpDownloadAssetException($strategies);
            }
        } else {
            $writer->setCacheDirectory($cacheDirectory);
        }
        
        $resultBuilder = new ChunkWriterResultBuilder($writer, 'savegame.xml');
        return new ExecutableStrategies($resultBuilder);
    }
}

