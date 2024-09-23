<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\ParameterFilters\ResourceParameterFilter;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ProxyResultBuilder;

class EditorTemplateBuilder implements ExecutableBuilderStrategyInterface {

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $game = $args->get(ResourceParameterFilter::PARAM_GAME);
        // $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
        // $user = $args->get(ResourceParameterFilter::PARAM_USER);
        $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);

        $url = $context->createUrl($args)->withPath("/games/$game/editor/$infosetId");
        $resultBuilder = new ProxyResultBuilder(Module::resolveToExecutable($url));
        return new ExecutableStrategies($resultBuilder);
    }
}

