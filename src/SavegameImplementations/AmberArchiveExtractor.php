<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\SavegameImplementations;

use Slothsoft\Amber\CLI\AmbTool;
use Slothsoft\Core\FileSystem;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveExtractor;
use DomainException;
use LogicException;
use SplFileInfo;

class AmberArchiveExtractor extends CopyArchiveExtractor {
    
    private AmbTool $ambtool;
    
    public function __construct(AmbTool $ambtool) {
        $this->ambtool = $ambtool;
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
                return parent::extractArchive($archivePath, $targetDir);
            default:
                throw new DomainException("Unknown AmbTool type '$type'!");
        }
    }
}

