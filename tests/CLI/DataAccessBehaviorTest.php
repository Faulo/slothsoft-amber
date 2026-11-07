<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

final class DataAccessBehaviorTest extends TestCase {
    public function backendProvider(): iterable {
        yield 'string' => ['string'];
        yield 'resource' => ['resource'];
        yield 'file' => ['file'];
    }
    
    private function createAccess(string $backend): DataAccessInterface {
        if ($backend === 'string') {
            return new StringDataAccess('abcd');
        }
        if ($backend === 'file') {
            $file = FileInfoFactory::createTempFile();
            file_put_contents((string) $file, 'abcd');
            return new FileDataAccess($file, StreamWrapperInterface::MODE_OPEN_READWRITE);
        }
        $resource = tmpfile();
        fwrite($resource, 'abcd');
        rewind($resource);
        return new ResourceDataAccess($resource);
    }
    
    /** @dataProvider backendProvider */
    public function testShortPeekPreservesPosition(string $backend): void {
        $access = $this->createAccess($backend);
        $access->setPosition(3);
        self::assertSame('d', $access->readString(4, true));
        self::assertSame(3, $access->getPosition());
        self::assertSame('d', $access->readString(1));
    }
    
    /** @dataProvider backendProvider */
    public function testWritesRespectPosition(string $backend): void {
        $access = $this->createAccess($backend);
        $access->setPosition(1);
        $access->writeString('X');
        $access->writeInteger(89, 1);
        self::assertSame(3, $access->getPosition());
        $access->setPosition(0);
        self::assertSame('aXYd', $access->readString(4));
    }
    
    /** @dataProvider backendProvider */
    public function testWritesPastEndPadWithZeroBytes(string $backend): void {
        $access = $this->createAccess($backend);
        $access->setPosition(6);
        $access->writeInteger(90, 1);
        self::assertSame(7, $access->getPosition());
        $access->setPosition(0);
        self::assertSame("abcd\0\0Z", $access->readString(7));
    }
    
    /** @dataProvider backendProvider */
    public function testEmptyReadPreservesPosition(string $backend): void {
        $access = $this->createAccess($backend);
        $access->setPosition(1);
        self::assertSame('', $access->readString(0));
        self::assertSame(1, $access->getPosition());
    }
    
    /** @dataProvider backendProvider */
    public function testReversePeekMatchesNextRead(string $backend): void {
        $source = $this->createAccess($backend);
        $source->setPosition(1);
        $access = new ProxyDataAccess($source, true);
        $access->setPosition(4);
        self::assertSame('cd', $access->readString(2, true));
        self::assertSame(4, $access->getPosition());
        self::assertSame(1, $source->getPosition());
        self::assertSame('cd', $access->readString(2));
        self::assertSame(2, $access->getPosition());
        self::assertSame(1, $source->getPosition());
        self::assertSame(98, $access->readInteger(1, true));
        self::assertSame(2, $access->getPosition());
        self::assertSame(98, $access->readInteger(1));
        self::assertSame(1, $source->getPosition());
    }
}
