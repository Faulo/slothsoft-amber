<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\SavegameImplementations;

use Slothsoft\Amber\CLI\AmbTool;
use Slothsoft\Core\FileSystem;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Savegame\Node\ArchiveParser\ArchiveExtractorInterface;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveExtractor;
use DomainException;
use LogicException;
use SplFileInfo;

final class AmberArchiveExtractor implements ArchiveExtractorInterface {
    
    private AmbTool $ambtool;
    
    private CopyArchiveExtractor $extractor;
    
    public function __construct(AmbTool $ambtool) {
        $this->ambtool = $ambtool;
        $this->extractor = new CopyArchiveExtractor();
    }
    
    public function extractArchive(SplFileInfo $archivePath, SplFileInfo $targetDir): bool {
        $type = $this->ambtool->inspectArchive($archivePath);
        switch ($type) {
            case AmbTool::TYPE_JH:
                // double-pass!
                $tempDir = FileInfoFactory::createTempFile();
                
                $this->ambtool->extractArchive($archivePath, $tempDir);
                
                $fileList = FileSystem::scanDir($tempDir->getRealPath(), FileSystem::SCANDIR_FILEINFO);
                
                if (count($fileList) !== 1) {
                    throw new LogicException("JH archive '$archivePath' must contain exactly 1 file");
                }
                
                return $this->extractArchive($fileList[0], $targetDir);
            case AmbTool::TYPE_AMBR:
                $this->ambtool->extractArchive($archivePath, $targetDir);
                return true;
            case '':
                return $this->extractor->extractArchive($archivePath, $targetDir);
            default:
                throw new DomainException("Unknown AmbTool type '$type'!");
        }
    }
}

