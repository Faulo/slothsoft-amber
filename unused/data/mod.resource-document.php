<?php
namespace Slothsoft\Farah;

use Slothsoft\Amber\ModController;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
return function (FarahUrl $url) {
    $controller = new ModController(__DIR__ . '/..');
    
    return $controller->resourceAction($url->getArguments());
};