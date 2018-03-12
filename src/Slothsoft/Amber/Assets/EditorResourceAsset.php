<?php
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Node\Asset\ContainerAsset;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;
use Slothsoft\Farah\Module\PathResolvers\PathResolverCatalog;
use Slothsoft\Farah\Module\PathResolvers\PathResolverInterface;
use Slothsoft\Farah\Module\Results\DOMWriterResult;
use Slothsoft\Farah\Module\Results\ResultInterface;

class EditorResourceAsset extends ContainerAsset
{
    protected function loadParameterFilter() : ParameterFilterInterface{
        return new ParameterFilter([]);
    }
}

