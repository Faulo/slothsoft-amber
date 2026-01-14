<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorParameters;
use Slothsoft\Amber\ParameterFilters\ResourceParameterFilter;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\Delegates\DOMWriterFromDOMWriterDelegate;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\DOMWriter\AssetDocumentDOMWriter;
use Slothsoft\Farah\Module\DOMWriter\AssetFragmentDOMWriter;
use Slothsoft\Farah\Module\DOMWriter\DOMWriterFileCacheByUrl;
use Slothsoft\Farah\Module\DOMWriter\TransformationDOMWriter;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;

final class AmberdataBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $repository = $args->get(ResourceParameterFilter::PARAM_REPOSITORY);
        $game = $args->get(ResourceParameterFilter::PARAM_GAME);
        $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
        $user = $args->get(ResourceParameterFilter::PARAM_USER);
        $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);
        
        $parameters = new EditorParameters($repository, $game, $version, $user, $infosetId);
        
        $amberdataUrl = $parameters->getProcessAmberdataUrl();
        $datasetUrl = $parameters->getProcessDatasetUrl();
        $templateUrl = $parameters->getStaticConvertUrl();
        $dictionaryUrl = $parameters->withInfoset('lib.dictionaries')->getProcessAmberdataUrl();
        
        $domDelegate = function () use ($amberdataUrl, $datasetUrl, $templateUrl, $dictionaryUrl): DOMWriterInterface {
            $writer = new AssetFragmentDOMWriter($amberdataUrl);
            $writer->appendChild(new AssetDocumentDOMWriter($datasetUrl));
            if ($dictionaryUrl !== $amberdataUrl) {
                $writer->appendChild(new AssetDocumentDOMWriter($dictionaryUrl));
            }
            $template = Module::resolveToDOMWriter($templateUrl);
            return new TransformationDOMWriter($writer, $template);
        };
        
        $writer = new DOMWriterFromDOMWriterDelegate($domDelegate);
        $writer = new DOMWriterFileCacheByUrl($amberdataUrl, $writer, __FILE__, (string) $datasetUrl, (string) $templateUrl);
        $resultBuilder = new FileWriterResultBuilder($writer, "$infosetId.xml");
        return new ExecutableStrategies($resultBuilder);
    }
}

