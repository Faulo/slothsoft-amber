<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Farah\Module\Node\Asset\ContainerAsset;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;

class EditorResourceAsset extends ContainerAsset
{

    protected function loadParameterFilter(): ParameterFilterInterface
    {
        return new ParameterFilter([]);
    }
}

