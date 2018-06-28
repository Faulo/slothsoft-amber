<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Farah\Exception\HttpDownloadAssetException;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;

class EditorBuilder extends AbstractResourceBuilder
{

    protected function processInfoset($infosetId) : ResultBuilderStrategyInterface
    {
        $request = (array) $this->args->get(EditorParameterFilter::PARAM_EDITOR_DATA);
        
        $action = $request['action'] ?? 'view';
        
        if ($action === 'upload') {
            $errors = $_FILES['save']['error'] ?? [];
            foreach ($errors as $archiveId => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $path = $_FILES['save']['tmp_name'][$archiveId] ?? '';
                    $this->editor->writeGameFile($archiveId, FileInfoFactory::createFromUpload($path));
                }
            }
        }
        
        if (isset($request['archiveId'])) {
            $archiveId = $request['archiveId'];
            $this->editor->loadArchive($archiveId);
            $this->editor->applyValues($request['data'] ?? []);
            
            $archive = $this->editor->getArchiveNode($archiveId);
            
            if ($action === 'save') {
                $this->editor->writeGameFile($archiveId, $archive);
            }
            
            if ($action === 'download') {
                $resultBuilder = new FileWriterResultBuilder($archive);
                $strategies = new ExecutableStrategies($resultBuilder);
                throw new HttpDownloadAssetException($strategies);
            }
            
        } else {
            $this->editor->loadNoArchives();
        }
        return new DOMWriterResultBuilder($this->editor);
    }
    
    protected function processArchive($infosetId, $archiveId) : ResultBuilderStrategyInterface
    {
        return $this->processInfoset($infosetId);
    }

    protected function processFile($infosetId, $archiveId, $fileId) : ResultBuilderStrategyInterface
    {
        return $this->processArchive($infosetId, $archiveId);
    }
    
    
    //         if ($uploadedArchives = $editor->getConfigValue('uploadedArchives')) {
    //             if (isset($uploadedArchives[$this->name])) {
    //                 move_uploaded_file($uploadedArchives[$this->name], $tempFile);
    //             }
    //         }
    //         if (isset($request['editor'])) {
    //             if (isset($request['editor']['action'])) {
    //                 switch ($request['editor']['action']) {
    //                     case 'download':
    //                         $resultBuilder = new NullResultBuilder();
    //                         foreach ($editorConfig['selectedArchives'] as $fileName => $tmp) {
    //                             if ($file = $editor->getArchiveFile($fileName)) {
    //                                 $resultBuilder = new FileWriterResultBuilder($file);
    //                                 break;
    //                             }
    //                         }
    //                         break;
    //                     case 'save':
    //                         foreach ($editorConfig['selectedArchives'] as $fileName => $tmp) {
    //                             $editor->writeArchiveFile($fileName);
    //                         }
    //                         break;
    //                 }
    //             }
    //         }
}

