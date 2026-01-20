<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorParameters;
use Slothsoft\Amber\ParameterFilters\EditorParameterFilter;
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
use Slothsoft\Farah\Module\DOMWriter\TransformationDOMWriter;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FromManifestInstructionBuilder;

final class EditorBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $instructions = (new FromManifestInstructionBuilder())->buildLinkInstructions($context, $args);
        
        $repository = $args->get(ResourceParameterFilter::PARAM_REPOSITORY);
        $game = $args->get(ResourceParameterFilter::PARAM_GAME);
        $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
        $user = $args->get(ResourceParameterFilter::PARAM_USER);
        $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);
        $save = $args->get(EditorParameterFilter::PARAM_EDITOR_DATA);
        
        $parameters = new EditorParameters($repository, $game, $version, $user, $infosetId, $save);
        
        $contextUrl = $context->createUrl($args);
        $editorUrl = $parameters->getProcessEditorDataUrl();
        $templateUrl = $parameters->getStaticEditorTemplateUrl();
        
        $instructions->stylesheetUrls->add(...$parameters->getProcessStylesheetUrls());
        
        $domDelegate = function () use ($contextUrl, $editorUrl, $templateUrl): DOMWriterInterface {
            $writer = new AssetFragmentDOMWriter($contextUrl);
            $writer->appendChild(new AssetDocumentDOMWriter($editorUrl, $editorUrl->getArguments()
                ->get(ResourceParameterFilter::PARAM_INFOSET_ID)));
            $template = Module::resolveToDOMWriter($templateUrl);
            return new TransformationDOMWriter($writer, $template);
        };
        
        $writer = new DOMWriterFromDOMWriterDelegate($domDelegate);
        $writer = new DecoratedDOMWriter($writer, $instructions->stylesheetUrls, $instructions->scriptUrls, $instructions->moduleUrls, $instructions->contentUrls);
        $resultBuilder = new DOMWriterResultBuilder($writer, "$infosetId.xml");
        return new ExecutableStrategies($resultBuilder);
    }
}

