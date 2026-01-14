<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorParameters;
use Slothsoft\Amber\ParameterFilters\ResourceParameterFilter;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\DOMWriter\DOMWriterFileCacheByUrl;
use Slothsoft\Farah\Module\DOMWriter\TransformationDOMWriterByUrls;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;

final class DictionaryBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $repository = $args->get(ResourceParameterFilter::PARAM_REPOSITORY);
        $game = $args->get(ResourceParameterFilter::PARAM_GAME);
        $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
        $user = $args->get(ResourceParameterFilter::PARAM_USER);
        $infosetId = 'lib.dictionaries';
        
        $parameters = new EditorParameters($repository, $game, $version, $user, $infosetId);
        
        $dictionaryUrl = $parameters->getProcessDictionaryUrl();
        $dataUrl = $parameters->getProcessAmberdataUrl();
        $templateUrl = $parameters->getStaticDictionaryTemplateUrl();
        
        $writer = new TransformationDOMWriterByUrls($dataUrl, $templateUrl);
        $writer = new DOMWriterFileCacheByUrl($dictionaryUrl, $writer, __FILE__, (string) $dataUrl, (string) $templateUrl);
        $resultBuilder = new FileWriterResultBuilder($writer, "$infosetId.xml");
        return new ExecutableStrategies($resultBuilder);
    }
}

