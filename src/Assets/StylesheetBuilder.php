<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\CLI\AmbGfx;
use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\ParameterFilters\ResourceParameterFilter;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\Adapter\FileWriterFromStringWriter;
use Slothsoft\Core\IO\Writable\Delegates\StringWriterFromStringDelegate;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\NullResultBuilder;

class StylesheetBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        if (! AmbGfx::isSupported()) {
            return new ExecutableStrategies(new NullResultBuilder());
        }
        
        $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);
        
        $writer = new StringWriterFromStringDelegate(function () use ($context, $args): string {
            $game = $args->get(ResourceParameterFilter::PARAM_GAME);
            $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
            $user = $args->get(ResourceParameterFilter::PARAM_USER);
            // $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);
            
            $controller = new EditorController();
            
            $config = $controller->createEditorConfig($game, $version, $user, 'gfx');
            $editor = $controller->createEditor($config);
            
            $contextUrl = $context->createUrl($args);
            $dataUrl = $contextUrl->withPath("/game-resources/amberdata");
            
            $dataDocument = DOMHelper::loadDocument((string) $dataUrl);
            
            $gfxNodeList = $dataDocument->getElementsByTagNameNS(DOMHelper::NS_AMBER_AMBERDATA, 'gfx');
            
            $imageData = [];
            $imageCoords = [];
            foreach ($gfxNodeList as $gfxNode) {
                $archiveId = $gfxNode->getAttribute('archive');
                if (! isset($imageData[$archiveId])) {
                    $imageData[$archiveId] = [
                        'width' => 0,
                        'height' => 0
                    ];
                    $imageData[$archiveId] = [];
                    $archiveNode = $editor->getArchiveNode($archiveId);
                    $archiveNode->load();
                    foreach ($archiveNode->getFileNodes() as $x => $fileNode) {
                        $fileNode->load();
                        foreach ($fileNode->getImageNodes() as $y => $imageNode) {
                            $imageData[$archiveId]['width'] = max($imageData[$archiveId]['width'], $imageNode->getWidth());
                            $imageData[$archiveId]['height'] = max($imageData[$archiveId]['height'], $imageNode->getHeight());
                            $imageCoords[$archiveId][] = [
                                'x' => $x,
                                'y' => $y
                            ];
                        }
                    }
                }
            }
            
            $css = [];
            
            foreach ($gfxNodeList as $gfxNode) {
                $archiveId = $gfxNode->getAttribute('archive');
                $fileId = $gfxNode->getAttribute('file');
                $paletteId = $gfxNode->getAttribute('palette');
                $gfxGroup = $gfxNode->getAttribute('group');
                $gfxId = $gfxNode->getAttribute('id');
                $gfxPosition = $gfxNode->getAttribute('position');
                $gfxLabel = $gfxNode->getAttribute('label');
                $gfxWidth = $gfxNode->getAttribute('target-width');
                $gfxHeight = $gfxNode->getAttribute('target-height');
                
                $x = $imageCoords[$archiveId][$gfxPosition]['x'];
                $y = $imageCoords[$archiveId][$gfxPosition]['y'];
                
                $imageStyle = '';
                $labelStyle = '';
                
                if ($x !== 0) {
                    $imageStyle .= sprintf('background-position-x: -%dem;', $x * $imageData[$archiveId]['width']);
                }
                if ($y !== 0) {
                    $imageStyle .= sprintf('background-position-y: -%dem;', $y * $imageData[$archiveId]['height']);
                }
                if ($fileId !== '') {
                    $imageStyle .= sprintf('background-image: url("/slothsoft@amber/game-resources/gfx?game=%s&version=%s&archiveId=%s&fileId=%03d&gfxId=%d&paletteId=%d");', $game, $version, $archiveId, $fileId, $gfxPosition, $paletteId);
                }
                if ($gfxWidth !== '' and $gfxHeight !== '') {
                    $imageStyle .= sprintf('width: %dem; height: %dem; background-size: %s;', $gfxWidth, $gfxHeight, '100%');
                }
                if ($gfxLabel !== '') {
                    $labelStyle .= sprintf('content: "%s";', $gfxLabel);
                }
                
                if ($imageStyle !== '') {
                    $css[] = "amber-{$gfxGroup}[value=\"$gfxId\"]::after { $imageStyle }";
                }
                if ($labelStyle !== '') {
                    $css[] = "amber-{$gfxGroup}[value=\"$gfxId\"]:hover::before { $labelStyle }";
                }
            }
            return implode(PHP_EOL, $css);
        }, "$infosetId.css");
        
        $writer = new FileWriterFromStringWriter($writer);
        $resultBuilder = new FileWriterResultBuilder($writer, "$infosetId.css");
        return new ExecutableStrategies($resultBuilder);
    }
}

