<?php
declare(strict_types = 1);
namespace Slothsoft\Amber;

use Slothsoft\Amber\CLI\AmbGfx;
use Slothsoft\Amber\CLI\AmbTool;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveBuilder;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveExtractor;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\FileSystem;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Savegame\Editor;
use Slothsoft\Savegame\Build\XmlBuilder;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveBuilder;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveExtractor;
use Error;
use Exception;
use InvalidArgumentException;

class ModController
{

    private $moduleDir;

    private $locator;

    private $dom;

    private $ambtool;

    private $ambgfx;

    public function __construct(string $moduleDir)
    {
        assert(strlen($moduleDir) and is_dir($moduleDir));
        
        $this->moduleDir = realpath($moduleDir);
    }

    public function defaultAction(FarahUrlArguments $args)
    {
        $ret = null;
        
        // assert($args->has('game'));
        // assert($args->has('mod'));
        
        $this->locator = new ModResourceLocator($this->moduleDir, $args->get('game', 'ambermoon'), $args->get('mod', 'Thalion-v1.05-DE'));
        $this->dom = new DOMHelper();
        
        $this->ambtool = new AmbTool($this->locator->getResourceById('ambtool')->getPath());
        $this->ambgfx = new AmbGfx($this->locator->getResourceById('ambgfx')->getPath());
        
        return $ret;
    }

    public function resourceAction(FarahUrlArguments $args): ModResource
    {
        $ret = $this->defaultAction($args);
        
        if ($id = $args->get('id')) {
            return $this->locator->getResourceById($id);
        }
        if ($type = $args->get('type') and $name = $args->get('name')) {
            return $this->locator->getResource($type, $name);
        }
        if ($lib = $args->get('lib')) {
            return $this->locator->getResource(ModResource::TYPE_LIBRARY, $lib);
        }
        
        throw new InvalidArgumentException("missing parameter to identify resource");
    }

    public function editorAction(FarahUrlArguments $args)
    {
        $ret = $this->defaultAction($args);
        
        // assert($args->has('struc'));
        
        $mode = $args->get('SaveDefault', 'thalion');
        $mode = preg_replace('~[^\w]~', '', $mode);
        $name = $args->get('SaveName', null);
        $name = preg_replace('~[^\w]~', '', $name);
        
        $loadAll = $args->has('LoadAll');
        $saveAll = $args->has('SaveAll');
        $downloadAll = $args->has('DownloadAll');
        
        $loadFile = $args->get('LoadFile', null);
        $saveFile = $args->get('SaveFile', null);
        $downloadFile = $args->get('DownloadFile', null);
        
        $request = (array) $args->get('save', []);
        
        $editorConfig = [];
        $editorConfig['structureFile'] = $this->locator->getResource(ModResource::TYPE_STRUCTURE, $args->get('struc', 'structure'))
            ->getPath();
        $editorConfig['defaultDir'] = $this->locator->getResource(ModResource::TYPE_MODFOLDER, 'src')->getPath();
        $editorConfig['userDir'] = $this->locator->getResource(ModResource::TYPE_MODFOLDER, 'user')->getPath();
        
        $editorConfig['mode'] = $mode;
        $editorConfig['id'] = $name;
        $editorConfig['loadAllArchives'] = ($loadAll or $saveAll or $downloadAll);
        $editorConfig['selectedArchives'] = [];
        $editorConfig['uploadedArchives'] = [];
        $editorConfig['archiveExtractors'] = $this->createArchiveExtractors();
        $editorConfig['archiveBuilders'] = $this->createArchiveBuilders();
        
        if (isset($request['editor'])) {
            if (isset($request['editor']['archives'])) {
                foreach ($request['editor']['archives'] as $val) {
                    $editorConfig['selectedArchives'][$val] = true;
                }
            }
        }
        if ($loadFile) {
            $editorConfig['selectedArchives'][$loadFile] = true;
        }
        if ($saveFile) {
            $editorConfig['selectedArchives'][$saveFile] = true;
        }
        if ($downloadFile) {
            $editorConfig['selectedArchives'][$downloadFile] = true;
        }
        
        if (isset($_FILES['save'])) {
            foreach ($_FILES['save']['tmp_name'] as $file => $filepath) {
                if (strlen($filepath) and file_exists($filepath)) {
                    $editorConfig['uploadedArchives'][$file] = $filepath;
                }
            }
        }
        
        $editor = new Editor($editorConfig);
        
        $editor->load();
        
        $editor->parseRequest($request);
        
        return $editor;
    }

    private $editorConfig = [
        /*
         * 'structure.savegame' => [
         * 'archives' => [
         * 'Party_char.amb',
         * 'Party_data.sav',
         * 'Merchant_data.amb',
         * 'Chest_data.amb',
         * ]
         * ],
         * //
         */
        'dictionaries' => [
            'structure' => 'structure', // .dictionaries
            'archives' => [
                'AM2_BLIT'
                /*
             * 'Party_char.amb',
             * 'NPC_char.amb',
             * 'Monster_char_data.amb',
             * //
             */
            ]
        ],
        'graphics' => [
            'archives' => [
                'Monster_char_data.amb'
            ]
        ],
        'items' => [
            'archives' => [
                'AM2_BLIT',
                'Object_texts.amb'
            ]
        ],
        'portraits' => [
            'archives' => []
        ],
        'maps' => [
            'archives' => [
                '2Map_data.amb',
                '2Map_texts.amb'
            ]
        ],
        'classes' => [
            'archives' => [
                'AM2_BLIT',
                'CONFIG_THALION'
            ]
        ],
        'tileset.icons' => [
            'archives' => [
                'Icon_data.amb'
            ]
        ],
        'tileset.labs' => [
            'archives' => [
                '2Lab_data.amb'
            ]
        ],
        'pcs' => [
            'archives' => [
                'Party_char.amb'
            ]
        ],
        'npcs' => [
            'archives' => [
                'NPC_char.amb'
            ]
        ],
        'monsters' => [
            'archives' => [
                'Monster_char_data.amb'
            ]
        ],
        'worldmap.morag' => [
            'archives' => [
                '2Map_data.amb',
                '2Map_texts.amb'
            ]
        ],
        'worldmap.kire' => [
            'archives' => [
                '3Map_data.amb',
                '3Map_texts.amb'
            ]
        ],
        'worldmap.lyramion' => [
            'archives' => [
                '1Map_data.amb',
                '1Map_texts.amb'
            ]
        ],
        'maps.2d' => [
            'archives' => [
                '2Map_data.amb',
                '2Map_texts.amb',
                '3Map_data.amb',
                '3Map_texts.amb'
            ]
        ],
        'maps.3d' => [
            'archives' => [
                '2Map_data.amb',
                '2Map_texts.amb',
                '3Map_data.amb',
                '3Map_texts.amb'
            ]
        ]
    ];

    public function createEditorAction(FarahUrlArguments $args)
    {
        $ret = false;
        
        $this->defaultAction($args);
        
        assert($args->has('lib'));
        $lib = $args->get('lib');
        
        assert(isset($this->editorConfig[$lib]));
        $config = $this->editorConfig[$lib];
        
        $struc = $config['structure'] ?? 'structure';
        
        $strucResource = $this->locator->getResource(ModResource::TYPE_STRUCTURE, $struc);
        
        $libResource = $this->locator->getResource(ModResource::TYPE_EDITOR, $lib);
        
        $args->set('struc', $struc);
        $args->set('save', [
            'editor' => [
                'archives' => $config['archives']
            ]
        ]);
        
        if ($libResource->exists() and $libResource->getChangeTime() > $strucResource->getChangeTime()) {
            $ret = true;
        } else {
            $editor = $this->editorAction($args);
            
            $builder = new XmlBuilder();
            $builder->registerTagBlacklist([
                'archive'
            ]);
            if ($lib === 'structure.savegame') {
                $builder->registerAttributeBlacklist([
                    'value',
                    'value-id'
                ]);
            } else {
                $builder->registerAttributeBlacklist([
                    'value-id',
                    'position',
                    'min',
                    'max',
                    'bit',
                    'encoding'
                ]);
            }
            $stream = $builder->buildStream($editor->getSavegame());
            
            $ret = $libResource->setContents($stream);
            
            fclose($stream);
        }
        
        return $ret ? $libResource : null;
    }

    public function createLibraryAction(FarahUrlArguments $args)
    {
        $ret = false;
        
        $this->defaultAction($args);
        
        assert($args->has('lib'));
        $lib = $args->get('lib');
        
        $libResource = $this->locator->getResource(ModResource::TYPE_LIBRARY, $lib);
        
        $editorResource = $this->locator->getResource(ModResource::TYPE_EDITOR, $lib);
        $templateResource = $this->locator->getResource(ModResource::TYPE_TEMPLATE, 'extract');
        
        if ($editorResource->exists() and $templateResource->exists()) {
            if ($libResource->exists() and $libResource->getChangeTime() > $editorResource->getChangeTime() and $libResource->getChangeTime() > $templateResource->getChangeTime()) {
                $ret = true;
            } else {
                $params = [];
                $params['lib'] = $lib;
                if ($lib !== 'dictionaries') {
                    $dictionaryResource = $this->locator->getResource(ModResource::TYPE_LIBRARY, 'dictionaries');
                    // $params['dictionaryURL'] = 'file://' . realpath($dictionaryResource->getPath());
                    $params['dictionaryURL'] = 'http://localhost' . $dictionaryResource->getUrl();
                }
                if ($libResource->ensureDirectory()) {
                    if ($this->dom->transformToFile($editorResource, $templateResource, $params, $libResource)) {
                        $ret = true;
                    }
                }
            }
        }
        
        return $ret ? $libResource : null;
    }

    public function createStyleAction(FarahUrlArguments $args)
    {
        $ret = false;
        
        $this->defaultAction($args);
        
        assert($args->has('lib'));
        $lib = $args->get('lib');
        
        $libResource = $this->locator->getResource(ModResource::TYPE_LIBRARY, $lib);
        $libDoc = $libResource->getDocument();
        
        $styling = [];
        
        switch ($lib) {
            case 'portraits':
                $fileId = 0;
                $palettes = [];
                foreach ([
                    49
                ] as $paletteId) {
                    $graphicResource = $this->locator->getResource(ModResource::TYPE_GRAPHIC, sprintf('%s/%03d-%02d', $lib, $fileId, $paletteId));
                    $palettes[$paletteId] = $graphicResource->getUrl();
                }
                $list = [];
                foreach ($libDoc->getElementsByTagName('portrait') as $itemNode) {
                    $list[$itemNode->getAttribute('id')] = $itemNode->getAttribute('id');
                }
                
                $styling[] = [
                    'name' => 'portrait',
                    'palettes' => $palettes,
                    'values' => $list
                ];
                break;
            case 'items':
                $fileId = 1;
                $palettes = [];
                foreach ([
                    49
                ] as $paletteId) {
                    $graphicResource = $this->locator->getResource(ModResource::TYPE_GRAPHIC, sprintf('%s/%03d-%02d', $lib, $fileId, $paletteId));
                    $palettes[$paletteId] = $graphicResource->getUrl();
                }
                $list = [];
                $labels = [];
                foreach ($libDoc->getElementsByTagName('item') as $itemNode) {
                    $list[$itemNode->getAttribute('id')] = $itemNode->getAttribute('image-id');
                    $labels[$itemNode->getAttribute('id')] = $itemNode->getAttribute('name');
                }
                
                $styling[] = [
                    'name' => 'item-id',
                    'palettes' => $palettes,
                    'values' => $list,
                    'labels' => $labels
                ];
                break;
            case 'tileset.icons':
                foreach ($libDoc->getElementsByTagName('tileset-icon') as $tilesetNode) {
                    $fileId = $tilesetNode->getAttribute('id');
                    $palettes = [];
                    foreach (range(1, 49) as $paletteId) {
                        $graphicResource = $this->locator->getResource(ModResource::TYPE_GRAPHIC, sprintf('%s/%03d-%02d', $lib, $fileId, $paletteId - 1));
                        $palettes[$paletteId] = $graphicResource->getUrl();
                    }
                    $list = [];
                    foreach ($tilesetNode->getElementsByTagName('tile') as $tileNode) {
                        $imageCount = (int) $tileNode->getAttribute('image-count');
                        if ($imageCount > 0) {
                            $list[$tileNode->getAttribute('id')] = (int) $tileNode->getAttribute('image-id');
                            $list[$tileNode->getAttribute('id')] --;
                        }
                    }
                    $styling[] = [
                        'selector' => sprintf('*[data-tileset-icon="%d"]', $fileId),
                        'name' => 'tile-id',
                        'palettes' => $palettes,
                        'values' => $list
                    ];
                }
                break;
            case 'pcs':
                break;
            case 'npcs':
                break;
            case 'monsters':
                
                // <xsl:variable name="id"
                // select="concat('monster-gfx-', generate-id(.))" />
                // <xsl:variable name="gfx-id" select=".//*[@name = 'gfx-id']/@value" />
                // <xsl:variable name="width"
                // select=".//*[@name = 'width']/*[@name = 'target']/@value" />
                // <xsl:variable name="height"
                // select=".//*[@name = 'height']/*[@name = 'target']/@value" />
                // <xsl:variable name="animation"
                // select="str:tokenize(string(.//*[@name = 'cycle']/@value))" />
                // <style scoped="scoped"><![CDATA[
                // .]]><xsl:value-of select="$id" /><![CDATA[ > *[data-picker-name="gfx-id"]::after {
                // width: ]]><xsl:value-of select="2 * $width" /><![CDATA[px;
                // height: ]]><xsl:value-of select="2 * $height" /><![CDATA[px;
                // font-size: ]]><xsl:value-of select="2 * $height" /><![CDATA[px;
                // background-image: url("/getResource.php/amber/monster-gfx/]]><xsl:value-of
                // select="$gfx-id" /><![CDATA[");
                // /*
                // animation-name: ]]><xsl:value-of select="$id" /><![CDATA[;
                // animation-iteration-count: infinite;
                // animation-timing-function: steps(1, end);
                // animation-duration: 4s;
                // */
                // }
                // @keyframes ]]><xsl:value-of select="$id" /><![CDATA[ {
                // ]]>
                // <xsl:for-each select="$animation">
                // <xsl:variable name="step" select="100 * position() div last()" />
                // <xsl:value-of
                // select="concat($step, '% { background-position-y: -', php:functionString('hexdec', .), 'em; } ')" />
                // </xsl:for-each>
                // <![CDATA[
                // }
                // ]]></style>
                break;
        }
        
        $css = [];
        
        foreach ($styling as $style) {
            if (! isset($style['selector'])) {
                $style['selector'] = '';
            }
            if (! isset($style['values'])) {
                $style['values'] = [];
            }
            if (! isset($style['labels'])) {
                $style['labels'] = [];
            }
            if (count($style['palettes']) > 1) {
                $css[] = sprintf('amber-%s::after {
background-clip: content-box;
background-size: cover;
background-repeat: no-repeat;
display: block;
content: " ";
}', $style['name']);
                foreach ($style['palettes'] as $paletteId => $url) {
                    $css[] = sprintf('%s[data-palette="%d"] amber-%s::after {
background-image: url("%s");
}', $style['selector'], $paletteId, $style['name'], $url);
                }
            } else {
                foreach ($style['palettes'] as $paletteId => $url) {
                    $css[] = sprintf('%s amber-%s::after {
background-image: url("%s");
background-clip: content-box;
background-size: cover;
background-repeat: no-repeat;
display: block;
content: " ";
}', $style['selector'], $style['name'], $url);
                }
            }
            foreach ($style['values'] as $id => $position) {
                $css[] = sprintf('%s amber-%s[value="%d"]::after { background-position-y: -%dem; }', $style['selector'], $style['name'], $id, $position);
            }
            foreach ($style['labels'] as $id => $label) {
                $css[] = sprintf('%s amber-%s[value="%d"]:hover::before { content: "%s"; }', $style['selector'], $style['name'], $id, $label);
            }
        }
        
        $css = implode(PHP_EOL, $css);
        
        $styleResource = $this->locator->getResource(ModResource::TYPE_STYLESHEET, $lib);
        $styleResource->setContents($css);
        return $styleResource;
    }

    public function installAction(FarahUrlArguments $args)
    {
        $gameList = [];
        $gameList[] = 'ambermoon';
        
        $modList = [];
        $modList[] = 'Thalion-v1.05-DE';
        $modList[] = 'Thalion-v1.06-DE';
        // $modList[] = 'Thalion-v1.07-DE';
        // $modList[] = 'Slothsoft-v1.00-DE';
        
        $editorList = [];
        // $editorList[] = 'structure.savegame';
        $editorList[] = 'dictionaries';
        $editorList[] = 'portraits';
        $editorList[] = 'items';
        $editorList[] = 'classes';
        $editorList[] = 'pcs';
        $editorList[] = 'npcs';
        $editorList[] = 'monsters';
        $editorList[] = 'tileset.icons';
        $editorList[] = 'tileset.labs';
        $editorList[] = 'maps.2d';
        $editorList[] = 'maps.3d';
        $editorList[] = 'worldmap.morag';
        $editorList[] = 'worldmap.kire';
        $editorList[] = 'worldmap.lyramion';
        $editorList[] = 'graphics';
        
        $libList = [];
        $libList[] = 'dictionaries';
        $libList[] = 'portraits';
        $libList[] = 'items';
        $libList[] = 'classes';
        $libList[] = 'pcs';
        $libList[] = 'npcs';
        $libList[] = 'monsters';
        $libList[] = 'tileset.icons';
        $libList[] = 'tileset.labs';
        $libList[] = 'maps.2d';
        $libList[] = 'maps.3d';
        $libList[] = 'worldmap.morag';
        $libList[] = 'worldmap.kire';
        $libList[] = 'worldmap.lyramion';
        $libList[] = 'graphics';
        
        $globalList = [];
        $globalList[] = 'dictionaries';
        $globalList[] = 'items';
        $globalList[] = 'portraits';
        $globalList[] = 'tileset.icons';
        $globalList[] = 'tileset.labs';
        
        $styleList = [];
        $styleList[] = 'portraits';
        $styleList[] = 'items';
        $styleList[] = 'pcs';
        $styleList[] = 'npcs';
        $styleList[] = 'monsters';
        $styleList[] = 'tileset.icons';
        
        $graphicsList = [];
        $graphicsList[] = 'graphics';
        
        foreach ($gameList as $game) {
            $args->set('game', $game);
            echo $game . PHP_EOL;
            foreach ($modList as $mod) {
                $args->set('mod', $mod);
                
                $this->defaultAction($args);
                
                $archiveManager = new ArchiveManager($this->locator->getResourceById('ambtool')->getPath());
                $graphicsManager = new GraphicsManager($this->locator->getResourceById('ambgfx')->getPath());
                
                echo "\t" . $mod . PHP_EOL;
                
                echo "\t\teditor" . PHP_EOL;
                foreach ($editorList as $lib) {
                    $args->set('lib', $lib);
                    
                    echo "\t\t\t" . $lib . PHP_EOL;
                    try {
                        $libResource = $this->createEditorAction($args);
                        $res = $libResource ? sprintf('Created resource "%s"!', $libResource->getName()) : sprintf('Failed to create resource "%s"!', $lib);
                    } catch (Exception $e) {
                        $res = 'EXCEPTION: ' . $e->getMessage();
                    } catch (Error $e) {
                        $res = 'ERROR: ' . $e->getMessage();
                    }
                    
                    echo "\t\t\t\t" . $res . PHP_EOL;
                }
                
                echo "\t\tlib" . PHP_EOL;
                $libs = [];
                foreach ($libList as $lib) {
                    $args->set('lib', $lib);
                    
                    echo "\t\t\t" . $lib . PHP_EOL;
                    try {
                        $libResource = $this->createLibraryAction($args);
                        if ($libResource and in_array($lib, $globalList, true)) {
                            if ($libDoc = $libResource->getDocument()) {
                                foreach ($libDoc->documentElement->childNodes as $node) {
                                    $libs[] = $libDoc->saveXML($node);
                                }
                            }
                        }
                        $res = $libResource ? sprintf('Created resource "%s"!', $libResource->getName()) : sprintf('Failed to create resource "%s"!', $lib);
                    } catch (Exception $e) {
                        $res = 'EXCEPTION: ' . $e->getMessage();
                    } catch (Error $e) {
                        $res = 'ERROR: ' . $e->getMessage();
                    }
                    
                    echo "\t\t\t\t" . $res . PHP_EOL;
                }
                $libs = '<amberdata xmlns="http://schema.slothsoft.net/amber/amberdata">' . PHP_EOL . implode(PHP_EOL, $libs) . PHP_EOL . '</amberdata>';
                $resource = $this->locator->setResourceContentsById('libs', $libs);
                $res = $resource ? sprintf('Created resource "%s"!', $resource->getName()) : sprintf('Failed to create resource "%s"!', 'libs');
                echo "\t\t\t" . $res . PHP_EOL;
                echo PHP_EOL;
                
                echo "\t\tstyle" . PHP_EOL;
                $styles = [];
                foreach ($styleList as $lib) {
                    $args->set('lib', $lib);
                    
                    echo "\t\t\t" . $lib . PHP_EOL;
                    try {
                        $libResource = $this->createStyleAction($args);
                        if ($libResource) {
                            $styles[] = $libResource->getContents();
                        }
                        $res = $libResource ? sprintf('Created resource "%s"!', $libResource->getName()) : sprintf('Failed to create resource "%s"!', $lib);
                    } catch (Exception $e) {
                        $res = 'EXCEPTION: ' . $e->getMessage();
                    } catch (Error $e) {
                        $res = 'ERROR: ' . $e->getMessage();
                    }
                    
                    echo "\t\t\t\t" . $res . PHP_EOL;
                }
                $styles = implode(PHP_EOL, $styles);
                $resource = $this->locator->setResourceContentsById('styles', $styles);
                $res = $resource ? sprintf('Created resource "%s"!', $resource->getName()) : sprintf('Failed to create resource "%s"!', 'styles');
                echo "\t\t\t" . $res . PHP_EOL;
                echo PHP_EOL;
                
                echo "\t\tgfx" . PHP_EOL;
                
                foreach ($graphicsList as $lib) {
                    $args->set('lib', $lib);
                    $graphicsResource = $this->createLibraryAction($args);
                    // $graphicsResource = $this->locator->getResourceById($lib);
                    $graphicsDoc = $graphicsResource->getDocument();
                    foreach ($graphicsDoc->getElementsByTagNameNS('http://schema.slothsoft.net/amber/amberdata', 'gfx-archive') as $archiveNode) {
                        $archiveName = $archiveNode->getAttribute('file-name');
                        $archivePath = $this->locator->getResource(ModResource::TYPE_SOURCE, $archiveNode->getAttribute('file-path'))
                            ->getPath();
                        echo "\t\t\t" . basename($archivePath) . PHP_EOL;
                        $archiveDir = temp_dir(__CLASS__);
                        $archiveManager->extractArchive($archivePath, $archiveDir);
                        
                        $filePathList = [];
                        foreach (FileSystem::scanDir($archiveDir) as $filePath) {
                            $filePathList[(int) $filePath] = $archiveDir . DIRECTORY_SEPARATOR . $filePath;
                        }
                        
                        foreach ($archiveNode->childNodes as $fileNode) {
                            $options = [];
                            foreach ($fileNode->attributes as $attr) {
                                $options[$attr->name] = $attr->value;
                            }
                            
                            switch ($fileNode->localName) {
                                case 'for-each-file':
                                    $fileIdList = array_keys($filePathList);
                                    break;
                                case 'file':
                                    $fileIdList = [
                                        $options['id']
                                    ];
                                    break;
                            }
                            
                            foreach ($fileIdList as $fileId) {
                                if (isset($filePathList[$fileId])) {
                                    $sourceFile = $filePathList[$fileId];
                                    $targetFile = $this->locator->getResource(ModResource::TYPE_GRAPHIC, $archiveName . DIRECTORY_SEPARATOR . sprintf('%03d-%02d', $fileId, $options['palette']))->getPath();
                                    
                                    if (! file_exists($targetFile)) {
                                        $res = $graphicsManager->convertGraphic($sourceFile, $targetFile, $options);
                                        echo "\t\t\t\t" . ($res ? 'OK: ' : 'ERROR: ') . $targetFile . PHP_EOL;
                                    }
                                }
                            }
                        }
                        echo PHP_EOL;
                    }
                }
            }
            echo PHP_EOL;
        }
    }

    public function createArchiveExtractors(): array
    {
        $ret = [];
        
        $amberExtractor = new AmberArchiveExtractor($this->ambtool);
        $ret[AmbTool::TYPE_AMBR] = $amberExtractor;
        $ret[AmbTool::TYPE_JH] = $amberExtractor;
        
        $copyExtractor = new CopyArchiveExtractor();
        $ret[AmbTool::TYPE_RAW] = $copyExtractor;
        $ret[AmbTool::TYPE_AM2] = $copyExtractor;
        
        return $ret;
    }

    public function createArchiveBuilders(): array
    {
        $ret = [];
        
        $amberBuilder = new AmberArchiveBuilder();
        $ret[AmbTool::TYPE_AMBR] = $amberBuilder;
        
        $copyBuilder = new CopyArchiveBuilder();
        $ret[AmbTool::TYPE_JH] = $copyBuilder;
        $ret[AmbTool::TYPE_RAW] = $copyBuilder;
        $ret[AmbTool::TYPE_AM2] = $copyBuilder;
        
        return $ret;
    }
}