<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorParameters;
use Slothsoft\Amber\ParameterFilters\ResourceParameterFilter;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\Delegates\DOMWriterFromDOMWriterDelegate;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\LinkDecorator\DecoratedDOMWriter;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\DOMWriter\AssetDocumentDOMWriter;
use Slothsoft\Farah\Module\DOMWriter\AssetFragmentDOMWriter;
use Slothsoft\Farah\Module\DOMWriter\DOMWriterFileCacheWithDependencies;
use Slothsoft\Farah\Module\DOMWriter\TransformationDOMWriter;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FromManifestInstructionBuilder;

final class ViewerBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $instructions = (new FromManifestInstructionBuilder())->buildLinkInstructions($context, $args);
        
        $repository = $args->get(ResourceParameterFilter::PARAM_REPOSITORY);
        $game = $args->get(ResourceParameterFilter::PARAM_GAME);
        $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
        $user = $args->get(ResourceParameterFilter::PARAM_USER);
        $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);
        
        $parameters = new EditorParameters($repository, $game, $version, $user, $infosetId);
        
        $amberdataUrl = $parameters->getProcessAmberdataUrl();
        $templateUrl = $parameters->getStaticViewerTemplateUrl();
        $dictionaryUrl = $parameters->withInfoset('lib.dictionaries')->getProcessAmberdataUrl();
        
        $instructions->stylesheetUrls->add(...$parameters->getProcessStylesheetUrls());
        
        $domDelegate = function () use ($amberdataUrl, $templateUrl, $dictionaryUrl): DOMWriterInterface {
            $writer = new AssetFragmentDOMWriter($amberdataUrl);
            $writer->appendChild(new AssetDocumentDOMWriter($amberdataUrl, $amberdataUrl->getArguments()
                ->get(ResourceParameterFilter::PARAM_INFOSET_ID)));
            if ($dictionaryUrl !== $amberdataUrl) {
                $writer->appendChild(new AssetDocumentDOMWriter($dictionaryUrl, $dictionaryUrl->getArguments()
                    ->get(ResourceParameterFilter::PARAM_INFOSET_ID)));
            }
            $template = Module::resolveToDOMWriter($templateUrl);
            return new TransformationDOMWriter($writer, $template);
        };
        
        $dependentFiles = [];
        $dependentFiles[] = __FILE__;
        $dependentFiles[] = (string) $context->getManifest()->createManifestFile('manifest.xml');
        $dependentFiles[] = (string) $amberdataUrl;
        $dependentFiles[] = (string) $templateUrl;
        foreach ($parameters->getStaticViewerGlobalUrls() as $globalUrl) {
            $dependentFiles[] = (string) $globalUrl;
        }
        
        $writer = new DOMWriterFromDOMWriterDelegate($domDelegate);
        $writer = new DecoratedDOMWriter($writer, $instructions->stylesheetUrls, $instructions->scriptUrls, $instructions->moduleUrls, $instructions->contentUrls);
        $writer = new DOMWriterFileCacheWithDependencies($writer, $context->createCacheFile("$infosetId.xml", $args), ...$dependentFiles);
        $resultBuilder = new FileWriterResultBuilder($writer, "$infosetId.xml");
        return new ExecutableStrategies($resultBuilder);
    }
}

