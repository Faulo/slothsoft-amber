<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;
use BadMethodCallException;

class SourceBuilder extends AbstractResourceBuilder
{
    protected function processInfoset(string $infosetId): ResultBuilderStrategyInterface
    {
        throw new BadMethodCallException("Missing parameter: archiveId");
    }
    protected function processArchive(string $infosetId, string $archiveId): ResultBuilderStrategyInterface
    {
        $this->editor->loadArchive($archiveId);
        $archiveNode = $this->editor->getArchiveNode($archiveId);
        return new FileWriterResultBuilder($archiveNode, $archiveId);
    }
    protected function processFile(string $infosetId, string $archiveId, string $fileId): ResultBuilderStrategyInterface
    {
        $this->editor->loadArchive($archiveId);
        $archiveNode = $this->editor->getArchiveNode($archiveId);
        $fileNode = $archiveNode->getFileNodeByName($fileId);
        return new FileWriterResultBuilder($fileNode, $fileId);
    }
}

