<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Core\ServerEnvironment;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\IO\Writable\Decorators\ChunkWriterFileCache;
use Slothsoft\Core\IO\Writable\Delegates\ChunkWriterFromChunkWriterDelegate;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ChunkWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;
use SplFileInfo;

class DatasetBuilder extends AbstractResourceBuilder
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
        $cacheFile[] = 'dataset';
        $cacheFile[] = "$infosetId.xml";
        $cacheFile = implode(DIRECTORY_SEPARATOR, $cacheFile);
        $cacheFile = FileInfoFactory::createFromPath($cacheFile);
        
        $editor = $this->editor;
        
        $delegate = function() use($editor) {
            $editor->loadAllArchives();
            $node = $editor->getSavegameNode();
            return $node->getChunkWriter();
        };
        
        $writer = new ChunkWriterFromChunkWriterDelegate($delegate);
        $writer = new ChunkWriterFileCache($writer, $cacheFile, function(SplFileInfo $cacheFile) { return false; });
        return new ChunkWriterResultBuilder($writer, $cacheFile->getFilename());
    }
    protected function processArchive(string $infosetId, string $archiveId): ResultBuilderStrategyInterface
    {
        return $this->processInfoset($infosetId);
    }
    protected function processFile(string $infosetId, string $archiveId, string $fileId): ResultBuilderStrategyInterface
    {
        return $this->processArchive($infosetId, $archiveId);
    }

}

