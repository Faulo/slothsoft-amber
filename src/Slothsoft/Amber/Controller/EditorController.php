<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Controller;

use Slothsoft\Amber\CLI\AmbGfx;
use Slothsoft\Amber\CLI\AmbTool;
use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveBuilder;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveExtractor;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlAuthority;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlPath;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;
use Slothsoft\Farah\Module\Node\Asset\AssetInterface;
use Slothsoft\Savegame\Editor;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveBuilder;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveExtractor;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlStreamIdentifier;

class EditorController
{

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

    public function createEditorConfig(FarahUrlArguments $args): array
    {
        $game = $args->get(ParameterFilter::PARAM_GAME);
        $mod = $args->get(ParameterFilter::PARAM_MOD);
        $preset = $args->get(ParameterFilter::PARAM_PRESET);
        
        $mode = $args->get(ParameterFilter::PARAM_SAVE_MODE);
        $mode = preg_replace('~[^\w]~', '', $mode);
        $name = $args->get(ParameterFilter::PARAM_SAVE_ID);
        $name = preg_replace('~[^\w]~', '', $name);
        
        $editorPreset = $this->editorPresetMap[$preset];
        
        $editorConfig = [];
        $editorConfig['structureFile'] = $this->getAmberAsset("/games/$game/$editorPreset[structure]")->getRealPath();
        $editorConfig['defaultDir'] = $this->getAmberAsset("/games/$game/mods/$mod")->getRealPath();
        $editorConfig['tempDir'] = temp_dir(__NAMESPACE__);
        
        $editorConfig['mode'] = $mode;
        $editorConfig['id'] = $name;
        $editorConfig['loadAllArchives'] = false;
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

    private function getAmberAsset(string $path): AssetInterface
    {
        return FarahUrlResolver::resolveToAsset(FarahUrl::createFromComponents(FarahUrlAuthority::createFromVendorAndModule('slothsoft', 'amber'), FarahUrlPath::createFromString($path), FarahUrlArguments::createEmpty(), FarahUrlStreamIdentifier::createEmpty()));
    }

    private function createAmbTool(): AmbTool
    {
        return new AmbTool($this->getAmberAsset('/cli/ambtool')->getRealPath());
    }

    private function createAmbGfx(): AmbGfx
    {
        return new AmbGfx($this->getAmberAsset('/cli/ambgfx')->getRealPath());
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

