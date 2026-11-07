<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\Controller;

use PHPUnit\Framework\TestCase;

/**
 * EditorParametersTest
 *
 * @see EditorParameters
 *
 */
final class EditorParametersTest extends TestCase {
    public function testEditorDataUrlWithoutEditorArguments(): void {
        $parameters = new EditorParameters('farah://slothsoft@amber/source', 'ambermoon', 'Thalion-v1.05-DE', 'alice', 'lib.items');
        $url = $parameters->getProcessEditorDataUrl();
        self::assertSame('/api/editor-data', (string) $url->getPath());
        self::assertSame('alice', $url->getArguments()->get('user'));
        self::assertSame('lib.items', $url->getArguments()->get('infosetId'));
    }
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(EditorParameters::class), "Failed to load class 'Slothsoft\Amber\Controller\EditorParameters'!");
    }
}
