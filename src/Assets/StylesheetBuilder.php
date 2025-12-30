<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\CLI\AmbGfx;
use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\ParameterFilters\ResourceParameterFilter;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\Delegates\ChunkWriterFromChunksDelegate;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ChunkWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\NullResultBuilder;
use DOMXPath;
use Generator;

class StylesheetBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        if (! AmbGfx::isSupported()) {
            return new ExecutableStrategies(new NullResultBuilder());
        }
        
        $contextUrl = $context->createUrl($args);
        $dataUrl = $contextUrl->withPath("/game-resources/amberdata");
        
        $game = $args->get(ResourceParameterFilter::PARAM_GAME);
        $version = $args->get(ResourceParameterFilter::PARAM_VERSION);
        $user = $args->get(ResourceParameterFilter::PARAM_USER);
        $infosetId = $args->get(ResourceParameterFilter::PARAM_INFOSET_ID);
        
        $writer = new ChunkWriterFromChunksDelegate(function () use ($dataUrl, $game, $version, $user): Generator {
            $controller = new EditorController();
            
            $config = $controller->createEditorConfig($game, $version, $user, 'gfx');
            $editor = $controller->createEditor($config);
            
            $dataDocument = Module::resolveToDOMWriter($dataUrl)->toDocument();
            
            $xpath = new DOMXPath($dataDocument);
            $xpath->registerNamespace('saa', DOMHelper::NS_AMBER_AMBERDATA);
            $gfxNodeList = $xpath->evaluate('//saa:gfx');
            
            $imageData = [];
            $imageCoords = [];
            foreach ($gfxNodeList as $gfxNode) {
                $archiveId = $gfxNode->getAttribute('archive');
                if (! isset($imageData[$archiveId])) {
                    $imageData[$archiveId] = [
                        'width' => 0,
                        'height' => 0
                    ];
                    $archiveNode = $editor->loadArchive($archiveId, true);
                    foreach ($archiveNode->getFileNodes() as $x => $fileNode) {
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
                
                $imageStyle = [];
                $labelStyle = [];
                
                if ($x !== 0) {
                    $imageStyle[] = sprintf('  background-position-x: -%dem;', $x * $imageData[$archiveId]['width']) . PHP_EOL;
                }
                if ($y !== 0) {
                    $imageStyle[] = sprintf('  background-position-y: -%dem;', $y * $imageData[$archiveId]['height']) . PHP_EOL;
                }
                if ($fileId !== '') {
                    $imageStyle[] = sprintf('  background-image: url("/slothsoft@amber/game-resources/gfx?game=%s&version=%s&archiveId=%s&fileId=%03d&gfxId=%d&paletteId=%d");', $game, $version, $archiveId, $fileId, $gfxPosition, $paletteId) . PHP_EOL;
                }
                if ($gfxWidth !== '') {
                    $imageStyle[] = sprintf('  width: %dem;', $gfxWidth) . PHP_EOL;
                }
                if ($gfxHeight !== '') {
                    $imageStyle[] = sprintf('  height: %dem;', $gfxHeight) . PHP_EOL;
                }
                if ($gfxLabel !== '') {
                    $labelStyle[] = sprintf('  content: "%s";', $gfxLabel) . PHP_EOL;
                }
                
                if ($imageStyle !== []) {
                    yield "amber-{$gfxGroup}[value=\"$gfxId\"]::after {" . PHP_EOL;
                    yield from $imageStyle;
                    yield '}' . PHP_EOL;
                }
                
                if ($labelStyle !== []) {
                    yield "amber-{$gfxGroup}[value=\"$gfxId\"]:hover::before {" . PHP_EOL;
                    yield from $labelStyle;
                    yield '}' . PHP_EOL;
                }
            }
        });
        
        $resultBuilder = new ChunkWriterResultBuilder($writer, "$infosetId.css");
        return new ExecutableStrategies($resultBuilder);
    }
}

