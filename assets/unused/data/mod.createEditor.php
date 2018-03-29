<?php
namespace Slothsoft\Farah;

use Slothsoft\Amber\ModController;
$controller = new ModController(__DIR__ . '/..');

return $controller->createEditorAction($this->httpRequest);