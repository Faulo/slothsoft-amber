<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Core\IO\RecursiveFileIterator;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\DOMWriter\ElementClosureDOMWriter;
use Slothsoft\Farah\Module\DOMWriter\FileInfoDOMWriter;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileInfoResultBuilder;
use DOMDocument;
use DOMElement;

class SourceBuilder implements ExecutableBuilderStrategyInterface
{

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies
    {
        $game = $args->get(SourceParameterFilter::PARAM_GAME);
        $version = $args->get(SourceParameterFilter::PARAM_VERSION);
        $id = $args->get(SourceParameterFilter::PARAM_ID);
        $user = $args->get(SourceParameterFilter::PARAM_USER);
        
        $controller = new EditorController();
        $editorConfig = $controller->createEditorConfig($game, $version, '', $user);
        $editor = $controller->createEditor($editorConfig);
        
        if ($id === '') {
            //generate index
            $delegate = function(DOMDocument $targetDoc) use ($editor) : DOMElement {
                $sourceDirectory = $editor->getDefaultDirectory();
                
                $writer = new FileInfoDOMWriter($sourceDirectory);
                $rootNode = $writer->toElement($targetDoc);
                
                $sourcePath = (string) $sourceDirectory;
                foreach (RecursiveFileIterator::iterateDirectory($sourcePath) as $sourceFile) {
                    $filePath = (string) $sourceFile;
                    if (strpos($filePath, $sourcePath) === 0) {
                        $filePath = substr($filePath, strlen($sourcePath) + 1);
                        
                        $userFile = $editor->buildUserFile($filePath);
                        $writer = new FileInfoDOMWriter($userFile->isFile() ? $userFile : $sourceFile);
                        $fileNode = $writer->toElement($targetDoc);
                        $fileNode->setAttribute('path', $filePath);
                        $rootNode->appendChild($fileNode);
                    }
                }
                
                return $rootNode;
            };
            $writer = new ElementClosureDOMWriter($delegate);
            $resultBuilder = new DOMWriterResultBuilder($writer);
        } else {
            //locate specific file
            $sourceFile = $editor->buildDefaultFile($id);
            $userFile = $editor->buildUserFile($id);
            
            $resultBuilder = new FileInfoResultBuilder($userFile->isFile() ? $userFile : $sourceFile);
        }
        return new ExecutableStrategies($resultBuilder);
    }
}

