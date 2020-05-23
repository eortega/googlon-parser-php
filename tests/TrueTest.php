<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class TrueTest extends TestCase
{
    public function testMyTest(): void
    {
        self::assertTrue(true);
    }
}
