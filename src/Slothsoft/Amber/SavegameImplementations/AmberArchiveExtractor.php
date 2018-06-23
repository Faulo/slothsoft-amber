<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\SavegameImplementations;

use Slothsoft\Amber\CLI\AmbTool;
use Slothsoft\Core\FileSystem;
use Slothsoft\Savegame\Node\ArchiveParser\ArchiveExtractorInterface;
use DomainException;
use Exception;

class AmberArchiveExtractor implements ArchiveExtractorInterface
{

    private $ambtool;

    public function __construct(AmbTool $ambtool)
    {
        $this->ambtool = $ambtool;
    }

    public function extractArchive(string $archivePath, string $targetDir): bool
    {
        $ret = false;
        if (!realpath($archivePath)) {
            throw new Exception("'$archivePath' is not a valid archive file.");
        }
        if (!realpath($targetDir)) {
            throw new Exception("'$targetDir' is not a valid extract directory.");
        }
        $archivePath = realpath($archivePath);
        $targetDir = realpath($targetDir);
        if ($archivePath and $targetDir) {
            $format = $this->ambtool->inspectArchive($archivePath);
            switch ($format) {
                case 'Format: JH (encrypted)':
                    // double-pass!
                    
                    $tempDir = temp_dir(__CLASS__); // . DIRECTORY_SEPARATOR . '_JH'
                    
                    $this->ambtool->exec($archivePath, $tempDir);
                    
                    $fileList = FileSystem::scanDir($tempDir, FileSystem::SCANDIR_REALPATH);
                    
                    assert(count($fileList) === 1, 'JH archive must contain 1 file');
                    
                    $output = $this->ambtool->exec($fileList[0], $targetDir);
                    
                    if (isset($output[1])) {
                        $ret = true;
                        break;
                    }
                // didn't need double-pass after all...
                case 'Format: AMBR (raw archive)':
                case 'Format: AMNP (compressed/encrypted archive)':
                case 'Format: AMNC (encrypted archive)':
                    $this->ambtool->exec($archivePath, $targetDir);
                    $ret = true;
                    break;
                case '':
                    $ret = copy($archivePath, $targetDir . DIRECTORY_SEPARATOR . '1');
                    break;
                default:
                    throw new DomainException(sprintf('unknown ambtool format "%s"!', $format));
            }
        }
        return $ret;
    }
}

