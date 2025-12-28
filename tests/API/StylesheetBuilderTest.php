<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\API;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsEqual;
use Slothsoft\Amber\Assets\StylesheetBuilder;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\FarahUrl\FarahUrlStreamIdentifier;
use Slothsoft\Farah\Module\Module;

final class StylesheetBuilderTest extends TestCase {
    
    /**
     *
     * @dataProvider performanceProvider
     */
    public function test_performance(string $ref, int $iterations): void {
        $url = FarahUrl::createFromReference($ref);
        
        $expected = file_get_contents($ref);
        
        $asset = Module::resolveToAsset($url);
        $executable = Module::resolveToExecutable($url);
        $result = Module::resolveToResult($url);
        
        $args = $result->createUrl()->getArguments();
        
        $sut = new StylesheetBuilder();
        
        for ($i = 0; $i < $iterations; $i ++) {
            $actual = $sut->buildExecutableStrategies($asset, $args)->resultBuilder->buildResultStrategies($executable, FarahUrlStreamIdentifier::createEmpty())->streamBuilder->buildStreamWriter($result)->toStream();
            
            $this->assertThat($actual, new IsEqual($expected));
        }
    }
    
    public function performanceProvider(): iterable {
        yield 'benchmark' => [
            'farah://slothsoft@amber/game-resources/stylesheet?infosetId=lib.items',
            100
        ];
    }
}
