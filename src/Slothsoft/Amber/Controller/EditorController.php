<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Controller;

use Slothsoft\Amber\CLI\AmbGfx;
use Slothsoft\Amber\CLI\AmbTool;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveBuilder;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveExtractor;
use Slothsoft\Core\ServerEnvironment;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Savegame\Editor;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveBuilder;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveExtractor;

class EditorController
{
    public function createEditorConfig(string $game, string $version, string $infoset, string $user): array
    {
        $user = preg_replace('~[^\w]~', '', $user);
        
        $editorConfig = [];
        $editorConfig['structureFile'] = (string) $this->getAmberAssetUrl("/games/$game/infoset/$infoset");
        $editorConfig['defaultDir'] = (string) $this->getAmberAssetPath("/games/$game/source/$version");
        $editorConfig['tempDir'] = ServerEnvironment::getCacheDirectory();
        
        $editorConfig['mode'] = 'thalion';
        $editorConfig['id'] = $user;
        $editorConfig['loadAllArchives'] = true;
        $editorConfig['selectedArchives'] = [];
        $editorConfig['uploadedArchives'] = [];
        $editorConfig['archiveExtractors'] = $this->createArchiveExtractors();
        $editorConfig['archiveBuilders'] = $this->createArchiveBuilders();
        
        return $editorConfig;
    }

    private $editorPresetMap = [
        'saveEditor' => [
            'structure' => 'structure.savegame',
            'archives' => []
        ],
        'gameEditor' => [
            'structure' => 'structure',
            'archives' => []
        ],
        'default' => [
            'structure' => 'structure.savegame',
            'archives' => []
        ],
        'raw' => [
            'structure' => 'structure.savegame',
            'archives' => [
                'Party_char.amb',
                'Party_data.sav',
                'Merchant_data.amb',
                'Chest_data.amb'
                /*
             * 'NPC_char.amb',
             * 'Monster_char_data.amb',
             * 'Dictionary.german',
             * 'NPC_texts.amb',
             * 'Party_texts.amb',
             * 'Icon_data.amb',
             * '2Lab_data.amb',
             * '3Lab_data.amb',
             * 'Place_data',
             * 'AM2_BLIT',
             * 'Object_texts.amb',
             * 'Abstract_data.amb',
             * //
             */
            ]
        ],
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
            'structure' => 'structure',
            'archives' => [
                'Monster_char_data.amb'
            ]
        ],
        'items' => [
            'structure' => 'structure',
            'archives' => [
                'AM2_BLIT',
                'Object_texts.amb'
            ]
        ],
        'portraits' => [
            'structure' => 'structure',
            'archives' => []
        ],
        'maps' => [
            'structure' => 'structure',
            'archives' => [
                '2Map_data.amb',
                '2Map_texts.amb'
            ]
        ],
        'classes' => [
            'structure' => 'structure',
            'archives' => [
                'AM2_BLIT',
                'CONFIG_THALION'
            ]
        ],
        'tileset.icons' => [
            'structure' => 'structure',
            'archives' => [
                'Icon_data.amb'
            ]
        ],
        'tileset.labs' => [
            'structure' => 'structure',
            'archives' => [
                '2Lab_data.amb'
            ]
        ],
        'pcs' => [
            'structure' => 'structure',
            'archives' => [
                'Party_char.amb'
            ]
        ],
        'npcs' => [
            'structure' => 'structure',
            'archives' => [
                'NPC_char.amb'
            ]
        ],
        'monsters' => [
            'structure' => 'structure',
            'archives' => [
                'Monster_char_data.amb'
            ]
        ],
        'worldmap.morag' => [
            'structure' => 'structure',
            'archives' => [
                '2Map_data.amb',
                '2Map_texts.amb'
            ]
        ],
        'worldmap.kire' => [
            'structure' => 'structure',
            'archives' => [
                '3Map_data.amb',
                '3Map_texts.amb'
            ]
        ],
        'worldmap.lyramion' => [
            'structure' => 'structure',
            'archives' => [
                '1Map_data.amb',
                '1Map_texts.amb'
            ]
        ],
        'maps.2d' => [
            'structure' => 'structure',
            'archives' => [
                '2Map_data.amb',
                '2Map_texts.amb',
                '3Map_data.amb',
                '3Map_texts.amb'
            ]
        ],
        'maps.3d' => [
            'structure' => 'structure',
            'archives' => [
                '2Map_data.amb',
                '2Map_texts.amb',
                '3Map_data.amb',
                '3Map_texts.amb'
            ]
        ]
    ];
    
    

    public function createEditorConfigOLD(string $game, string $mod, string $preset, string $saveMode, string $saveId): array
    {
        $saveMode = preg_replace('~[^\w]~', '', $saveMode);
        $saveId = preg_replace('~[^\w]~', '', $saveId);
        
        $editorPreset = $this->editorPresetMap[$preset];
        
        $editorConfig = [];
        $editorConfig['structureFile'] = (string) $this->getAmberAssetUrl("/games/$game/$editorPreset[structure]");
        $editorConfig['defaultDir'] = (string) $this->getAmberAssetPath("/games/$game/source/$mod");
        $editorConfig['tempDir'] = temp_dir(__NAMESPACE__);
        
        $editorConfig['mode'] = $saveMode;
        $editorConfig['id'] = $saveId;
        $editorConfig['loadAllArchives'] = true;
        $editorConfig['selectedArchives'] = [];
        $editorConfig['uploadedArchives'] = [];
        $editorConfig['archiveExtractors'] = $this->createArchiveExtractors();
        $editorConfig['archiveBuilders'] = $this->createArchiveBuilders();
        
        foreach ($editorPreset['archives'] as $archive) {
            $editorConfig['selectedArchives'][$archive] = true;
        }
        
        return $editorConfig;
    }

    public function createEditor(array $editorConfig): Editor
    {
        $editor = new Editor($editorConfig);
        $editor->load();
        return $editor;
    }
    
    private function getAmberAssetUrl(string $url): FarahUrl {
        return FarahUrl::createFromReference($url, FarahUrl::createFromReference('farah://slothsoft@amber'));
    }
    
    private function getAmberAssetPath(string $url): string {
        $url = $this->getAmberAssetUrl($url);
        return Module::resolveToAsset($url)->getManifestElement()->getAttribute('realpath');
    }

    private function createAmbTool(): AmbTool
    {
        return new AmbTool((string) $this->getAmberAssetUrl('/cli/ambtool'));
    }

    private function createAmbGfx(): AmbGfx
    {
        return new AmbGfx((string) $this->getAmberAssetUrl('/cli/ambgfx'));
    }

    private function createArchiveExtractors(): array
    {
        $ret = [];
        
        $amberExtractor = new AmberArchiveExtractor($this->createAmbTool());
        $ret[AmbTool::TYPE_AMBR] = $amberExtractor;
        $ret[AmbTool::TYPE_JH] = $amberExtractor;
        
        $copyExtractor = new CopyArchiveExtractor();
        $ret[AmbTool::TYPE_RAW] = $copyExtractor;
        $ret[AmbTool::TYPE_AM2] = $copyExtractor;
        
        return $ret;
    }

    private function createArchiveBuilders(): array
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

