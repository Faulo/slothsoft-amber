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
use SplFileInfo;
use Slothsoft\Savegame\EditorConfig;

class EditorController
{

    public function createEditorConfig(string $game, string $version, string $user, string $infoset): EditorConfig
    {
//         $user = preg_replace('~[^\w]~', '', $user);
        if ($game === '') {
            throw new \InvalidArgumentException('$game must not be empty.');
        }
        if ($version === '') {
            throw new \InvalidArgumentException('$version must not be empty.');
        }
        if ($user === '') {
            throw new \InvalidArgumentException('$user must not be empty.');
        }
        if ($infoset === '') {
            throw new \InvalidArgumentException('$infoset must not be empty.');
        }
        return new EditorConfig(
            $this->getAmberAssetPath("/games/$game/source/$version"),
            new SplFileInfo(ServerEnvironment::getDataDirectory() . "/slothsoft/amber/$game/$version/$user"),
            new SplFileInfo(ServerEnvironment::getCacheDirectory() . "/slothsoft/amber/$game/$version/savegame/$infoset"),
            new SplFileInfo((string) $this->getAmberAssetUrl("/games/$game/infoset/$infoset")),
            $this->createArchiveExtractors(),
            $this->createArchiveBuilders()
        );
    }
    public function createEditor(EditorConfig $config): Editor
    {
        return new Editor($config);
    }
    
    private function getAmberAssetUrl(string $url): FarahUrl
    {
        return FarahUrl::createFromReference($url, FarahUrl::createFromReference('farah://slothsoft@amber'));
    }
    
    private function getAmberAssetPath(string $url): SplFileInfo
    {
        $url = $this->getAmberAssetUrl($url);
        return Module::resolveToAsset($url)->getFileInfo();
    }
    
    public function createAmbTool(): AmbTool
    {
        return new AmbTool((string) $this->getAmberAssetPath('/cli/ambtool'));
    }
    
    public function createAmbGfx(): AmbGfx
    {
        return new AmbGfx((string) $this->getAmberAssetPath('/cli/amgfx'));
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

    
}

