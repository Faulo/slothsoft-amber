<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\ParameterFilters\EditorParameterFilter;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Farah\Exception\HttpDownloadAssetException;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ChunkWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;
use SplFileInfo;

class EditorBuilder implements ExecutableBuilderStrategyInterface {

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $game = $args->get(EditorParameterFilter::PARAM_GAME);
        $version = $args->get(EditorParameterFilter::PARAM_VERSION);
        $user = $args->get(EditorParameterFilter::PARAM_USER);
        $infosetId = $args->get(EditorParameterFilter::PARAM_INFOSET_ID);

        $request = (array) $args->get(EditorParameterFilter::PARAM_EDITOR_DATA);

        $controller = new EditorController();
        $config = $controller->createEditorConfig($game, $version, $user, $infosetId);
        $editor = $controller->createEditor($config);

        $action = $request['action'] ?? 'view';

        if ($action === 'upload') {
            $errors = $_FILES['save']['error'] ?? [];
            foreach ($errors as $archiveId => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $path = $_FILES['save']['tmp_name'][$archiveId] ?? '';
                    $editor->writeGameFile($archiveId, FileInfoFactory::createFromUpload($path));
                }
            }
        }

        if (isset($request['archiveId'])) {
            $archiveId = $request['archiveId'];
            $editor->loadArchive($archiveId);
            $editor->applyValues($request['data'] ?? []);

            $archive = $editor->getArchiveNode($archiveId);

            if ($action === 'save') {
                $editor->writeGameFile($archiveId, $archive);
            }

            if ($action === 'download') {
                $resultBuilder = new FileWriterResultBuilder($archive, $archive->getArchiveId());
                $strategies = new ExecutableStrategies($resultBuilder);
                throw new HttpDownloadAssetException($strategies);
            }
        } else {
            $editor->load();
        }

        $savegame = $editor->getSavegameNode();

        $shouldRefreshCacheDelegate = function (SplFileInfo $cacheFile) {
            return true;
        };

        $writer = $savegame->getChunkWriter();
        // $writer = new ChunkWriterFileCache($writer, FileInfoFactory::createTempFile(), $shouldRefreshCacheDelegate);

        $resultBuilder = new ChunkWriterResultBuilder($writer, 'savegame.editor.xml');
        return new ExecutableStrategies($resultBuilder);
    }

    // if ($uploadedArchives = $editor->getConfigValue('uploadedArchives')) {
    // if (isset($uploadedArchives[$this->name])) {
    // move_uploaded_file($uploadedArchives[$this->name], $tempFile);
    // }
    // }
    // if (isset($request['editor'])) {
    // if (isset($request['editor']['action'])) {
    // switch ($request['editor']['action']) {
    // case 'download':
    // $resultBuilder = new NullResultBuilder();
    // foreach ($editorConfig['selectedArchives'] as $fileName => $tmp) {
    // if ($file = $editor->getArchiveFile($fileName)) {
    // $resultBuilder = new FileWriterResultBuilder($file);
    // break;
    // }
    // }
    // break;
    // case 'save':
    // foreach ($editorConfig['selectedArchives'] as $fileName => $tmp) {
    // $editor->writeArchiveFile($fileName);
    // }
    // break;
    // }
    // }
    // }
}

