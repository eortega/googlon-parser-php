<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use GooglonParser\Algorithms\Sort\Radix;

class RadixTest extends TestCase
{

    public function testGetLengthOfLargestItem()
    {
        $radix = new Radix(['abcd', 'aq', 'aqwertyha', 'a'], [], 'a');
        $reflectionClass = new ReflectionClass($radix);
        $method = $reflectionClass->getMethod('getLengthOfLargestItem');
        $method->setAccessible(true);
        $r = $method->invoke($radix);
        self::assertEquals(9, $r);
    }

    public function testCreateBucket()
    {
        $alphabet = ['a' => 0, 'b' => 1, 'c' => 2];
        $radix = new Radix(['abc', 'c', 'bca', 'cab'], $alphabet, 'a');
        $reflectionClass = new ReflectionClass($radix);
        $method = $reflectionClass->getMethod('createBucket');
        $method->setAccessible(true);
        $bucket = $method->invoke($radix);
        $expected = ['a' => [], 'b' => [], 'c' => []];
        self::assertEquals($expected, $bucket);
    }

    public function testFlatBucket()
    {
        $alphabet = ['a' => 0, 'b' => 1, 'c' => 2];
        $radix = new Radix(['cb', 'abc', 'c', 'bca', 'cab'], $alphabet, 'a');

        $reflectionClass = new ReflectionClass($radix);
        $method = $reflectionClass->getMethod('flatBucket');
        $method->setAccessible(true);
        $bucket = [
            'a' => ['abc'],
            'b' => ['bca'],
            'c' => ['cb', 'c', 'cab']
        ];
        $flattened = $method->invoke($radix, $bucket);

        $expected = [
            'abc',
            'bca',
            'cb',
            'c',
            'cab',
        ];
        self::assertEquals($expected, $flattened);
    }

    public function testSort()
    {
        $alphabet = ['a' => 0, 'b' => 1, 'c' => 2];
        $radix = new Radix(['cb', 'abc', 'c', 'a', 'b' ,'bca', 'cab'], $alphabet, 'a');
        $sorted = $radix->sort();
        $expected = [
            'a',
            'abc',
            'b',
            'bca',
            'c',
            'cab',
            'cb',
        ];
        self::assertCount(7 , $sorted);
        self::assertEquals($expected, $sorted);
    }
}