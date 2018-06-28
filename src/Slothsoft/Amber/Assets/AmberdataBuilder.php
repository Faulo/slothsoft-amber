<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Farah\Module\Asset\LinkInstructionCollection;
use Slothsoft\Farah\Module\Asset\UseInstructionCollection;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\TransformationResultBuilder;

class AmberdataBuilder extends AbstractResourceBuilder
{
    protected function processInfoset(string $infosetId): ResultBuilderStrategyInterface
    {
        $game = $this->args->get(AmberdataParameterFilter::PARAM_GAME);
        
        $contextUrl = $this->asset->createUrl($this->args);
        $convertUrl = $contextUrl->withPath("/games/$game/convert/$infosetId");
        $datasetUrl = $contextUrl->withPath("/game-resources/dataset");
        $dictionaryUrl = $infosetId === 'dictionaries'
            ? null
            : $contextUrl->withPath("/games/$game/dictionaries");
        
        $getUseInstructions = function () use ($contextUrl, $convertUrl, $datasetUrl, $dictionaryUrl): UseInstructionCollection {
            $instructions = new UseInstructionCollection();
            $instructions->rootUrl = $contextUrl;
            $instructions->templateUrl = $convertUrl;
            $instructions->documentUrls[] = $datasetUrl;
            if ($dictionaryUrl) {
                $instructions->documentUrls[] = $dictionaryUrl;
            }
            return $instructions;
        };
        $getLinkInstructions = function () : LinkInstructionCollection {
            return new LinkInstructionCollection();
        };
        return new TransformationResultBuilder($getUseInstructions, $getLinkInstructions);
    }

    protected function processFile(string $infosetId, string $archiveId, string $fileId): ResultBuilderStrategyInterface
    {
        return $this->processInfoset($infosetId);
    }

    protected function processArchive(string $infosetId, string $archiveId): ResultBuilderStrategyInterface
    {
        return $this->processInfoset($infosetId);
    }

}

