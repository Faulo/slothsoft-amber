<?php
declare(strict_types = 1);
namespace tests\Slothsoft\Amber;

use PHPUnit\Framework\TestCase;
use Slothsoft\Amber\ModController;
use AssertionError;



class ModControllerTest extends TestCase
{
    public function testThrowsAssertionWhenPathNotFound()
    {
        $this->expectException(AssertionError::class);
        
        new ModController('');
    }
}
