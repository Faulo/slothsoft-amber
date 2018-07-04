<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Core\ServerEnvironment;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\IO\Writable\Decorators\DOMWriterFileCache;
use Slothsoft\Core\IO\Writable\Delegates\DOMWriterFromDOMWriterDelegate;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\DOMWriter\AssetDocumentDOMWriter;
use Slothsoft\Farah\Module\DOMWriter\AssetFragmentDOMWriter;
use Slothsoft\Farah\Module\DOMWriter\TransformationDOMWriter;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;
use SplFileInfo;

class AmberdataBuilder extends AbstractResourceBuilder
{
    protected function processInfoset(string $infosetId): ResultBuilderStrategyInterface
    {
        $game = $this->args->get(DatasetParameterFilter::PARAM_GAME);
        $version = $this->args->get(DatasetParameterFilter::PARAM_VERSION);
        
        $cacheFile = [];
        $cacheFile[] = ServerEnvironment::getCacheDirectory();
        $cacheFile[] = 'slothsoft/amber';
        $cacheFile[] = $game;
        $cacheFile[] = $version;
        $cacheFile[] = 'amberdata';
        $cacheFile[] = "$infosetId.xml";
        $cacheFile = implode(DIRECTORY_SEPARATOR, $cacheFile);
        $cacheFile = FileInfoFactory::createFromPath($cacheFile);
        
        $editor = $this->editor;
        $asset = $this->asset;
        $args = $this->args;
        
        $delegate = function() use($editor, $asset, $args, $game, $infosetId) {
            $contextUrl = $this->asset->createUrl($this->args);
            $convertUrl = $contextUrl->withPath("/games/$game/convert/$infosetId");
            $datasetUrl = $contextUrl->withPath("/game-resources/dataset");
            $dictionaryUrl = $infosetId === 'lib.dictionaries'
                ? null
                : $contextUrl->withQuery('infosetId=lib.dictionaries');
                
            
            $writer = new AssetFragmentDOMWriter($contextUrl);
            $writer->appendChild(new AssetDocumentDOMWriter($datasetUrl));
            if ($dictionaryUrl) {
                $writer->appendChild(new AssetDocumentDOMWriter($dictionaryUrl));
            }
            $template = Module::resolveToDOMWriter($convertUrl);
            return new TransformationDOMWriter($writer, $template);
        };
        
        $writer = new DOMWriterFromDOMWriterDelegate($delegate);
        $writer = new DOMWriterFileCache($writer, $cacheFile, function(SplFileInfo $cacheFile) { return false; });
        return new FileWriterResultBuilder($writer, $cacheFile->getFilename());
    }

    protected function processFile(string $infosetId, string $archiveId, string $fileId): ResultBuilderStrategyInterface
    {
        return $this->processInfoset($infosetId);
    }

    protected function processArchive(string $infosetId, string $archiveId): ResultBuilderStrategyInterface
    {
        return $this->processInfoset($infosetId);
    }

}

