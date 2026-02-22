<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\AmberUser;
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
        
        $hasSaveData = ($save and $action !== EditorParameterFilter::PARAM_EDITOR_ACTION_VIEW);
        if ($hasSaveData) {
            $user = AmberUser::getNewIdIfDefault($user);
        }
        
        $parameters = new EditorParameters($repository, $game, $version, $user, $infosetId);
        
        $controller = new EditorController();
        $config = $controller->createEditorConfig($parameters);
        $editor = $controller->createEditor($config);
        
        if ($action === EditorParameterFilter::PARAM_EDITOR_ACTION_UPLOAD) {
            $errors = $_FILES[EditorParameterFilter::PARAM_EDITOR_UPLOAD]['error'] ?? [];
            foreach ($errors as $path => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $archivePath = $path;
                    $uploadPath = $_FILES[EditorParameterFilter::PARAM_EDITOR_UPLOAD]['tmp_name'][$path] ?? '';
                    $editor->writeGameFile($path, FileInfoFactory::createFromUpload($uploadPath));
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
        
        /** @var ArchiveNode $archive */
        foreach ($archives as $archivePath => $archive) {
            $isDownload = $download === $archivePath;
            
            if (isset($save[$archivePath]) and is_array($save[$archivePath])) {
                $editor->applyValues($save[$archivePath]);
                
                if ($action === EditorParameterFilter::PARAM_EDITOR_ACTION_SAVE or $isDownload) {
                    $editor->writeGameFile($archivePath, $archive);
                }
            }
            
            if ($isDownload) {
                $editor = $controller->createEditor($config);
                $archive = $editor->loadArchive($archivePath, true);
                
                $resultBuilder = new FileWriterResultBuilder($archive, $archive->getName());
                $strategies = new ExecutableStrategies($resultBuilder);
                throw new HttpDownloadAssetException($strategies);
            }
        }
        
        $resultBuilder = new ChunkWriterResultBuilder($writer, 'savegame.xml');
        return new ExecutableStrategies($resultBuilder);
    }
}

