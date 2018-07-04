<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Executables;

use Slothsoft\Core\IO\Writable\Adapter\DOMWriterFromFileWriter;
use Slothsoft\Farah\FarahUrl\FarahUrlStreamIdentifier;
use Slothsoft\Farah\Module\Executable\Executable;
use Slothsoft\Farah\Module\Executable\ExecutableInterface;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;
use Slothsoft\Farah\Module\Result\ResultStrategies;
use Slothsoft\Farah\Module\Result\StreamBuilderStrategy\DOMWriterStreamBuilder;
use Slothsoft\Farah\Module\Result\StreamBuilderStrategy\FileWriterStreamBuilder;
use Slothsoft\Savegame\Editor;

class GameResourceBuilder implements ResultBuilderStrategyInterface
{
    public function __construct(Editor $editor, string $archiveId = '', $fileId = '') {
        
    }
    public function buildResultStrategies(ExecutableInterface $context, FarahUrlStreamIdentifier $type): ResultStrategies
    {
        if ($type === Executable::resultIsXml()) {
            return new ResultStrategies(new DOMWriterStreamBuilder(new DOMWriterFromFileWriter($this->writer)));
        } else {
            return new ResultStrategies(new FileWriterStreamBuilder($this->writer));
        }
    }

}

