<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;
use Slothsoft\Savegame\Editor;

abstract class AbstractResourceBuilder implements ExecutableBuilderStrategyInterface
{
    /**
     * @var AssetInterface
     */
    protected $asset;
    
    /**
     * @var FarahUrlArguments
     */
    protected $args;
    
    /**
     * @var Editor
     */
    protected $editor;

    final public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies
    {
        $game = $args->get(AbstractParameterFilter::PARAM_GAME);
        $version = $args->get(AbstractParameterFilter::PARAM_VERSION);
        $user = $args->get(AbstractParameterFilter::PARAM_USER);
        
        $infosetId = (string) $args->get(AbstractParameterFilter::PARAM_INFOSET_ID);
        $archiveId = (string) $args->get(AbstractParameterFilter::PARAM_ARCHIVE_ID);
        $fileId = (string) $args->get(AbstractParameterFilter::PARAM_FILE_ID);
        
        $controller = new EditorController();
        
        $config = $controller->createEditorConfig($game, $version, $user, $infosetId);
        
        $this->asset = $context;
        $this->args = $args;
        $this->editor = $controller->createEditor($config);
        
        if ($fileId === '') {
            if ($archiveId === '') {
                $resultBuilder = $this->processInfoset($infosetId);
            } else {
                $resultBuilder = $this->processArchive($infosetId, $archiveId);
            }
        } else {
            $resultBuilder = $this->processFile($infosetId, $archiveId, $fileId);
        }
        
        return new ExecutableStrategies($resultBuilder);
    }
    
    abstract protected function processInfoset(string $infosetId) : ResultBuilderStrategyInterface;
    abstract protected function processArchive(string $infosetId, string $archiveId) : ResultBuilderStrategyInterface;
    abstract protected function processFile(string $infosetId, string $archiveId, string $fileId) : ResultBuilderStrategyInterface;
    
}

