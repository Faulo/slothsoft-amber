<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\ParameterFilters\ResourceParameterFilter;
use Slothsoft\Core\ServerEnvironment;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\Decorators\DOMWriterFileCache;
use Slothsoft\Core\IO\Writable\Delegates\DOMWriterFromDOMWriterDelegate;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\DOMWriter\AssetDocumentDOMWriter;
use Slothsoft\Farah\Module\DOMWriter\AssetFragmentDOMWriter;
use Slothsoft\Farah\Module\DOMWriter\TransformationDOMWriter;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;
use SplFileInfo;

class AmberdataBuilder implements ExecutableBuilderStrategyInterface {

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $game = $args->get(ResourceParameterFilter::PARAM_GAME);
        $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
        // $user = $args->get(ResourceParameterFilter::PARAM_USER);
        $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);

        $cacheFile = [];
        $cacheFile[] = ServerEnvironment::getCacheDirectory();
        $cacheFile[] = 'slothsoft/amber';
        $cacheFile[] = $game;
        $cacheFile[] = $version;
        $cacheFile[] = 'amberdata';
        $cacheFile[] = "$infosetId.xml";
        $cacheFile = implode(DIRECTORY_SEPARATOR, $cacheFile);
        $cacheFile = FileInfoFactory::createFromPath($cacheFile);

        $domDelegate = function () use ($context, $args, $game, $infosetId): DOMWriterInterface {
            $contextUrl = $context->createUrl($args);
            $convertUrl = $contextUrl->withPath("/games/$game/convert/$infosetId");
            $datasetUrl = $contextUrl->withPath("/game-resources/dataset");
            $dictionaryUrl = $infosetId === 'lib.dictionaries' ? null : $contextUrl->withQuery('infosetId=lib.dictionaries');

            $writer = new AssetFragmentDOMWriter($contextUrl);
            $writer->appendChild(new AssetDocumentDOMWriter($datasetUrl));
            if ($dictionaryUrl) {
                $writer->appendChild(new AssetDocumentDOMWriter($dictionaryUrl));
            }
            $template = Module::resolveToDOMWriter($convertUrl);
            return new TransformationDOMWriter($writer, $template);
        };

        $shouldRefreshDelegate = function (SplFileInfo $cacheFile): bool {
            // return $config->infosetFile->getMTime() > $cacheFile->getMTime();
            return false;
        };

        $writer = new DOMWriterFromDOMWriterDelegate($domDelegate);
        $writer = new DOMWriterFileCache($writer, $cacheFile, $shouldRefreshDelegate);
        $resultBuilder = new FileWriterResultBuilder($writer, "$infosetId.xml");
        return new ExecutableStrategies($resultBuilder);
    }
}

