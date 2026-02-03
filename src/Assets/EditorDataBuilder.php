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
use Slothsoft\Savegame\Node\ArchiveNode;

final class EditorDataBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $repository = $args->get(EditorParameterFilter::PARAM_REPOSITORY);
        $game = $args->get(EditorParameterFilter::PARAM_GAME);
        $version = $args->get(EditorParameterFilter::PARAM_VERSION);
        $user = $args->get(EditorParameterFilter::PARAM_USER);
        $infosetId = $args->get(EditorParameterFilter::PARAM_INFOSET_ID);
        
        $archivePath = $args->get(EditorParameterFilter::PARAM_ARCHIVE_ID);
        $action = $args->get(EditorParameterFilter::PARAM_EDITOR_ACTION);
        $download = $args->get(EditorParameterFilter::PARAM_EDITOR_DOWNLOAD);
        $save = $args->get(EditorParameterFilter::PARAM_EDITOR_DATA);
        
        $parameters = new EditorParameters($repository, $game, $version, $user, $infosetId);
        
        $controller = new EditorController();
        $config = $controller->createEditorConfig($parameters);
        $editor = $controller->createEditor($config);
        
        if ($action === EditorParameterFilter::PARAM_EDITOR_ACTION_UPLOAD) {
            $errors = $_FILES[EditorParameterFilter::PARAM_EDITOR_UPLOAD]['error'] ?? [];
            foreach ($errors as $archivePath => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $path = $_FILES[EditorParameterFilter::PARAM_EDITOR_UPLOAD]['tmp_name'][$archivePath] ?? '';
                    $editor->writeGameFile($archivePath, FileInfoFactory::createFromUpload($path));
                }
            }
        }
        
        $savegame = $editor->getSavegameNode();
        $cacheDirectory = $config->cacheDirectory . DIRECTORY_SEPARATOR . 'editor-data';
        
        $writer = new XmlBuilder($savegame);
        
        $archives = [];
        switch ($archivePath) {
            case '':
                $writer->setCacheDirectory($cacheDirectory);
                break;
            case '_':
                $writer->setCacheDirectory($cacheDirectory . DIRECTORY_SEPARATOR . '_');
                $editor->loadSavegame(true, true);
                /** @var ArchiveNode $archive */
                foreach ($savegame->getArchiveNodes() as $archive) {
                    $archivePath = $archive->getArchivePath();
                    $archives[$archivePath] = $archive;
                }
                break;
            default:
                $writer->setCacheDirectory($cacheDirectory . DIRECTORY_SEPARATOR . $archivePath);
                $archives[$archivePath] = $editor->loadArchive($archivePath, true);
                break;
        }
        
        foreach ($archives as $archivePath => $archive) {
            if (isset($save[$archivePath]) and is_array($save[$archivePath])) {
                $editor->applyValues($save[$archivePath]);
            }
            
            if ($action === EditorParameterFilter::PARAM_EDITOR_ACTION_SAVE) {
                $editor->writeGameFile($archivePath, $archive);
            }
            
            if ($download === $archivePath) {
                $resultBuilder = new FileWriterResultBuilder($archive, $archive->getName());
                $strategies = new ExecutableStrategies($resultBuilder);
                throw new HttpDownloadAssetException($strategies);
            }
        }
        
        $resultBuilder = new ChunkWriterResultBuilder($writer, 'savegame.xml');
        return new ExecutableStrategies($resultBuilder);
    }
}

