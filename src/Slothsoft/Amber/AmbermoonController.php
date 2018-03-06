<?php
namespace Slothsoft\Amber;

use Slothsoft\Amber\Assets\EditorAsset;
use Slothsoft\Farah\Module\Controllers\ControllerImplementation;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\PathResolvers\PathResolverCatalog;
use Slothsoft\Farah\Module\PathResolvers\PathResolverInterface;
use Slothsoft\Farah\Module\Results\NullResult;
use Slothsoft\Farah\Module\Results\ResultInterface;

class AmbermoonController extends ControllerImplementation
{
    public function createResult(FarahUrl $url): ResultInterface
    {
        return new NullResult($url);
    }
    public function createPathResolver(): PathResolverInterface
    {
        $asset = $this->getAsset();
        $resultMap = [];
        $resultMap['/editor'] = new EditorAsset('ambermoon');
        $resultMap['/editor']->initModuleNode($asset->getOwnerModule(), $asset->getElement(), []);
        return PathResolverCatalog::createMapPathResolver($this->getAsset(), $resultMap);
    }
}

