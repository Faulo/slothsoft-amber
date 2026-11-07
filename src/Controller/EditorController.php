<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\Controller;

use Slothsoft\Amber\CLI\AmbGfx;
use Slothsoft\Amber\CLI\AmbTool;
use Slothsoft\Amber\CLI\NativeExecutable;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveBuilder;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveExtractor;
use Slothsoft\Amber\SavegameImplementations\AmberExecutableBuilder;
use Slothsoft\Amber\SavegameImplementations\AmberExecutableExtractor;
use Slothsoft\Savegame\Editor;
use Slothsoft\Savegame\EditorConfig;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveBuilder;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveExtractor;
use SplFileInfo;

class EditorController {
    
    public function createEditorConfig(EditorParameters $parameters): EditorConfig {
        $sourceDirectory = $parameters->getRepositorySourceDirectory();
        $userDirectory = $parameters->getServerDataDirectory();
        $cacheDirectory = $parameters->getServerCacheDirectory();
        $infosetFile = $parameters->getStaticInfosetFile();
        $archiveExtractors = $this->createArchiveExtractors();
        $archiveBuilders = $this->createArchiveBuilders($sourceDirectory);
        
        return new EditorConfig($sourceDirectory, $userDirectory, $cacheDirectory, $infosetFile, $archiveExtractors, $archiveBuilders);
    }
    
    public function createEditor(EditorConfig $config): Editor {
        return new Editor($config);
    }
    
    public function createAmbTool(): AmbTool {
        return new AmbTool(NativeExecutable::requirePath('ambtool'));
    }
    
    public function createAmbGfx(): AmbGfx {
        return new AmbGfx(NativeExecutable::requirePath('amgfx'));
    }
    
    private function createArchiveExtractors(): array {
        $ret = [];
        
        $amberExtractor = new AmberArchiveExtractor($this->createAmbTool());
        $ret[AmbTool::TYPE_AMBR] = $amberExtractor;
        $ret[AmbTool::TYPE_JH] = $amberExtractor;
        
        $copyExtractor = new CopyArchiveExtractor();
        $ret[AmbTool::TYPE_RAW] = $copyExtractor;
        
        $executableExtractor = new AmberExecutableExtractor();
        $ret[AmbTool::TYPE_AM2] = $executableExtractor;
        
        return $ret;
    }
    
    private function createArchiveBuilders(SplFileInfo $sourceDirectory): array {
        $ret = [];
        
        $amberBuilder = new AmberArchiveBuilder();
        $ret[AmbTool::TYPE_AMBR] = $amberBuilder;
        
        $copyBuilder = new CopyArchiveBuilder();
        $ret[AmbTool::TYPE_JH] = $copyBuilder;
        $ret[AmbTool::TYPE_RAW] = $copyBuilder;
        
        $ret[AmbTool::TYPE_AM2] = new AmberExecutableBuilder($sourceDirectory);
        
        return $ret;
    }
}
