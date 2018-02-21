<?php
namespace Slothsoft\Farah;

use Slothsoft\Amber\ModController;

return function(array $args) {
    $controller = new ModController(__DIR__ . '/..');
    
    $request = Kernel::getInstance()->getRequest();
    
    return $controller->resourceAction($request);
};