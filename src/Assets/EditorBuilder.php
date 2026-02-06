<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\AmberUser;
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
use Slothsoft\Farah\Module\DOMWriter\TranslationDOMWriter2;
use Slothsoft\Farah\Dictionary;

final class EditorBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $instructions = (new FromManifestInstructionBuilder())->buildLinkInstructions($context, $args);
        
        $repository = $args->get(ResourceParameterFilter::PARAM_REPOSITORY);
        $game = $args->get(ResourceParameterFilter::PARAM_GAME);
        $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
        $user = $args->get(ResourceParameterFilter::PARAM_USER);
        $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);
        
        $archivePath = $args->get(EditorParameterFilter::PARAM_ARCHIVE_ID);
        $action = $args->get(EditorParameterFilter::PARAM_EDITOR_ACTION);
        $download = $args->get(EditorParameterFilter::PARAM_EDITOR_DOWNLOAD);
        $save = $args->get(EditorParameterFilter::PARAM_EDITOR_DATA);
        
        $hasSaveData = ($save and $action !== EditorParameterFilter::PARAM_EDITOR_ACTION_VIEW);
        if ($hasSaveData) {
            $user = AmberUser::getNewIdIfDefault($user);
        }
        
        $parameters = new EditorParameters($repository, $game, $version, $user, $infosetId);
        $parameters = $parameters->withEditorArgs($archivePath, $action, $download, $save);
        
        $userArgs = FarahUrlArguments::createFromValueList([
            ResourceParameterFilter::PARAM_USER => AmberUser::getNewIdIfDefault($user)
        ]);
        $contextUrl = $context->createUrl($args)->withAdditionalQueryArguments($userArgs, true);
        $editorUrl = $parameters->getProcessEditorDataUrl();
        $templateUrl = $parameters->getStaticEditorTemplateUrl();
        $dictionaryUrl = $parameters->withInfoset('lib.dictionaries')->getProcessAmberdataUrl();
        
        $instructions->stylesheetUrls->add(...$parameters->getProcessStylesheetUrls());
        
        $domDelegate = function () use ($contextUrl, $editorUrl, $templateUrl, $dictionaryUrl): DOMWriterInterface {
            $writer = new AssetFragmentDOMWriter($contextUrl);
            $writer->appendChild(new AssetDocumentDOMWriter($editorUrl, $editorUrl->getArguments()
                ->get(ResourceParameterFilter::PARAM_INFOSET_ID)));
            $writer->appendChild(new AssetDocumentDOMWriter($dictionaryUrl, $dictionaryUrl->getArguments()
                ->get(ResourceParameterFilter::PARAM_INFOSET_ID)));
            
            $template = Module::resolveToDOMWriter($templateUrl);
            return new TransformationDOMWriter($writer, $template);
        };
        
        $writer = new DOMWriterFromDOMWriterDelegate($domDelegate);
        $writer = new DecoratedDOMWriter($writer, $instructions->stylesheetUrls, $instructions->scriptUrls, $instructions->moduleUrls, $instructions->contentUrls);
        if (! $instructions->dictionaryUrls->isEmpty()) {
            $dict = Dictionary::getInstance();
            $dict->setLang('de-de');
            $writer = new TranslationDOMWriter2($writer, Dictionary::getInstance(), $instructions->dictionaryUrls);
        }
        $resultBuilder = new DOMWriterResultBuilder($writer, "$infosetId.xml");
        return new ExecutableStrategies($resultBuilder);
    }
}

