<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;

class DatasetBuilder extends AbstractResourceBuilder
{
    protected function processInfoset(string $infosetId): ResultBuilderStrategyInterface
    {
        $this->editor->loadAllArchives();
        return new DOMWriterResultBuilder($this->editor);
    }
    protected function processArchive(string $infosetId, string $archiveId): ResultBuilderStrategyInterface
    {
        $this->editor->loadArchive($archiveId);
        return new DOMWriterResultBuilder($this->editor);
    }
    protected function processFile(string $infosetId, string $archiveId, string $fileId): ResultBuilderStrategyInterface
    {
        return $this->processArchive($infosetId, $archiveId);
    }

}

