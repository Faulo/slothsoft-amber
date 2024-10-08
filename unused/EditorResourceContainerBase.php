<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Executables\AmberExecutableCreator;
use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Node\Asset\AssetBase;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;
use Slothsoft\Farah\Module\PathResolvers\PathResolverCatalog;
use Slothsoft\Farah\Module\PathResolvers\PathResolverInterface;
use Slothsoft\Farah\Module\Node\Asset\AssetInterface;

abstract class EditorResourceContainerBase extends AssetBase
{

    abstract protected function getSelfAssetList(): array;

    abstract protected function getChildAssetList(): array;

    abstract protected function inventChildAsset(string $assetName): AssetInterface;

    protected function loadParameterFilter(): ParameterFilterInterface
    {
        return new ParameterFilter([
            'id' => ''
        ]);
    }

    protected function loadExecutable(FarahUrlArguments $args): ExecutableInterface
    {
        if ($resourceId = $args->get('id')) {
            return $this->traverseTo("/$resourceId")->lookupExecutable($args);
        } else {
            $executables = [];
            foreach ($this->getSelfAssetList() as $assetName) {
                $asset = $this->traverseTo("/$assetName");
                $executables[] = $asset->lookupExecutable($args);
            }
            
            $creator = new AmberExecutableCreator($this, $args);
            return $creator->createExecutableMerger($executables);
        }
    }

    protected function loadPathResolver(): PathResolverInterface
    {
        $map = [
            '/' => $this
        ];
        foreach ($this->getChildAssetList() as $assetName) {
            $map["/$assetName"] = $this->inventChildAsset($assetName);
        }
        return PathResolverCatalog::createMapPathResolver($this, $map);
    }
}

