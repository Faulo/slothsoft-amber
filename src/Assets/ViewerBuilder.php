<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorParameters;
use Slothsoft\Amber\ParameterFilters\ResourceParameterFilter;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\DOMWriter\DOMWriterFileCacheWithDependencies;
use Slothsoft\Farah\Module\DOMWriter\TransformationDOMWriterByUrls;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;

final class ViewerBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $repository = $args->get(ResourceParameterFilter::PARAM_REPOSITORY);
        $game = $args->get(ResourceParameterFilter::PARAM_GAME);
        $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
        $user = $args->get(ResourceParameterFilter::PARAM_USER);
        $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);
        
        $parameters = new EditorParameters($repository, $game, $version, $user, $infosetId);
        
        $amberdataUrl = $parameters->getProcessAmberdataUrl();
        $templateUrl = $parameters->getStaticViewerTemplateUrl();
        
        $writer = new TransformationDOMWriterByUrls($amberdataUrl, $templateUrl);
        $writer = new DOMWriterFileCacheWithDependencies($writer, $context->createCacheFile("$infosetId.xml", $args), __FILE__, (string) $amberdataUrl, (string) $templateUrl);
        $resultBuilder = new FileWriterResultBuilder($writer, "$infosetId.xml");
        return new ExecutableStrategies($resultBuilder);
    }
}

