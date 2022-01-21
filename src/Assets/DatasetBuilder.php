<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\ParameterFilters\ResourceParameterFilter;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\Delegates\ChunkWriterFromChunkWriterDelegate;
use Slothsoft\Core\IO\Writable\Delegates\ChunkWriterFromChunksDelegate;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ChunkWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\NullResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ProxyResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;
use Slothsoft\Savegame\Editor;
use Slothsoft\Savegame\EditorConfig;
use Slothsoft\Savegame\Build\BuildableInterface;
use Slothsoft\Savegame\Build\BuilderInterface;
use Slothsoft\Savegame\Build\XmlBuilder;
use Generator;

class DatasetBuilder implements ExecutableBuilderStrategyInterface {

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        if (PHP_OS_FAMILY !== 'Windows') {
            return new ExecutableStrategies(new NullResultBuilder());
        }

        $game = $args->get(ResourceParameterFilter::PARAM_GAME);
        $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
        $user = $args->get(ResourceParameterFilter::PARAM_USER);

        $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);
        $archiveId = $args->get(ResourceParameterFilter::PARAM_ARCHIVE_ID);
        $fileId = $args->get(ResourceParameterFilter::PARAM_FILE_ID);

        $controller = new EditorController();

        $config = $controller->createEditorConfig($game, $version, $user, $infosetId);

        if ($fileId === '') {
            if ($archiveId === '') {
                if ($infosetId === '') {
                    $url = "farah://slothsoft@amber/games/$game/infoset";
                    $url = FarahUrl::createFromReference($url);
                    $resultBuilder = new ProxyResultBuilder(Module::resolveToExecutable($url));
                } else {
                    $resultBuilder = $this->processInfoset($config, $infosetId);
                }
            } else {
                $resultBuilder = $this->processArchive($config, $infosetId, $archiveId);
            }
        } else {
            $resultBuilder = $this->processFile($config, $infosetId, $archiveId, $fileId);
        }

        return new ExecutableStrategies($resultBuilder);
    }

    private function processInfosets(): ResultBuilderStrategyInterface {
        $chunkDelegate = function (): Generator {
            yield '<xml>';
            yield '</xml>';
        };
        $writer = new ChunkWriterFromChunksDelegate($chunkDelegate);
        return new ChunkWriterResultBuilder($writer, "infosets.xml");
    }

    private function processInfoset(EditorConfig $config, string $infosetId): ResultBuilderStrategyInterface {
        $chunkDelegate = function () use ($config, $infosetId): ChunkWriterInterface {
            $editor = new Editor($config);
            $editor->load();
            $savegameNode = $editor->getSavegameNode();
            return $this->createXmlBuilder((string) $config->cacheDirectory, $savegameNode);
        };
        $writer = new ChunkWriterFromChunkWriterDelegate($chunkDelegate);
        return new ChunkWriterResultBuilder($writer, "$infosetId.xml");
    }

    private function processArchive(EditorConfig $config, string $infosetId, string $archiveId): ResultBuilderStrategyInterface {
        $chunkDelegate = function () use ($config, $infosetId, $archiveId): ChunkWriterInterface {
            $editor = new Editor($config);
            $editor->loadArchive($archiveId);
            $savegameNode = $editor->getSavegameNode();
            $archiveNode = $savegameNode->getArchiveById($archiveId);
            return $this->createXmlBuilder((string) $config->cacheDirectory, $archiveNode);
        };
        $writer = new ChunkWriterFromChunkWriterDelegate($chunkDelegate);
        return new ChunkWriterResultBuilder($writer, "$infosetId.$archiveId.xml");
    }

    private function processFile(EditorConfig $config, string $infosetId, string $archiveId, string $fileId): ResultBuilderStrategyInterface {
        $chunkDelegate = function () use ($config, $infosetId, $archiveId, $fileId): ChunkWriterInterface {
            $editor = new Editor($config);
            $editor->loadArchive($archiveId);
            $savegameNode = $editor->getSavegameNode();
            $archiveNode = $savegameNode->getArchiveById($archiveId);
            $fileNode = $archiveNode->getFileNodeByName($fileId);
            $fileNode->load();
            return $this->createXmlBuilder((string) $config->cacheDirectory, $fileNode);
        };
        $writer = new ChunkWriterFromChunkWriterDelegate($chunkDelegate);
        return new ChunkWriterResultBuilder($writer, "$infosetId.$archiveId.$fileId.xml");
    }

    private function createXmlBuilder(string $cacheDirectory, BuildableInterface $node): BuilderInterface {
        $builder = new XmlBuilder($node);
        $builder->registerTagBlacklist([]);
        $builder->registerAttributeBlacklist([
            'position',
            'bit',
            'encoding'
        ]);

        $builder->registerTagCachelist([
            $node->getBuildTag()
        ]);
        $builder->setCacheDirectory($cacheDirectory);

        return $builder;
    }
}

