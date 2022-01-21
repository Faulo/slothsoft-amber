<?php
namespace Slothsoft\Farah;

use Slothsoft\Amber\ModController;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
return function (FarahUrl $url) {
    $args = $url->getArguments();
    
    // $kernel = Kernel::getInstance();
    // $httpRequest = $kernel->getRequest();
    // $httpResponse = $kernel->getResponse();
    
    $controller = new ModController(__DIR__ . '/..');
    
    $editor = $controller->editorAction($args);
    
    $saveAll = $httpRequest->hasInputValue('SaveAll');
    $downloadAll = $httpRequest->hasInputValue('DownloadAll');
    
    $saveFile = $httpRequest->getInputValue('SaveFile', null);
    $downloadFile = $httpRequest->getInputValue('DownloadFile', null);
    
    if ($saveAll or $downloadAll) {
        if ($saveAll) {
            // todo
        }
        if ($downloadAll) {
            $httpResponse->setDownload(true);
            return $editor->asFile();
        }
    }
    
    if ($saveFile or $downloadFile) {
        if ($saveFile) {
            $editor->writeArchiveFile($saveFile);
        }
        if ($downloadFile) {
            $httpResponse->setDownload(true);
            return $editor->getArchiveFile($downloadFile);
        }
    }
    
    return $editor->asDocument();
};