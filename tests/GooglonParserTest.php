<?php
declare(strict_types=1);

namespace eortega\GooglonParser\Tests;

use PHPUnit\Framework\TestCase;
use eortega\GooglonParser\GooglonParser;

class GooglonParserTest extends TestCase
{

    protected static function loadCases()
    {
        return include 'TestCases.php';
    }

    public function testPrepositions()
    {
        $testCases = self::loadCases();
        foreach ($testCases as $case) {
            $tokens = GooglonParser::tokenize($case['text']);
            $actualPrepositions = array_filter($tokens, 'eortega\GooglonParser\GooglonParser::isPreposition');
            self::assertCount($case['e']['propositions'], $actualPrepositions);
        }
    }

    public function testVerbs()
    {
        $testCases = self::loadCases();
        foreach ($testCases as $case) {
            $tokens = GooglonParser::tokenize($case['text']);
            $actualVerbs = array_filter($tokens, 'eortega\GooglonParser\GooglonParser::isVerb');
            self::assertCount($case['e']['verbs'], $actualVerbs);
        }
    }

    public function testSubjunctiveVerbs()
    {
        $testCases = self::loadCases();
        foreach ($testCases as $case) {
            $tokens = GooglonParser::tokenize($case['text']);
            $actualSubjVerbs = array_filter($tokens, 'eortega\GooglonParser\GooglonParser::isSubjunctiveVerb');
            self::assertCount($case['e']['subjVerbs'], $actualSubjVerbs);
        }
    }

    public function testPrettyNumbers()
    {
        $testCases = self::loadCases();
        foreach ($testCases as $case) {
            $tokens = GooglonParser::tokenize($case['text']);
            $numbersList = array_map('eortega\GooglonParser\GooglonParser::wordToNumber', $tokens);
            $prettyNumbers = array_filter($numbersList, 'eortega\GooglonParser\GooglonParser::isPrettyNumber');
            self::assertCount($case['e']['distinctPrettyNumbers'], $prettyNumbers);
        }
    }

    public function testLexicographicalSort()
    {
        $testCases = self::loadCases();
        foreach ($testCases as $case) {
            $tokens = GooglonParser::tokenize($case['text']);
            $lexicallyOrdered = GooglonParser::lexicographicalSort(array_unique($tokens));
            self::assertEquals($case['e']['sorted'], $lexicallyOrdered);
        }
    }
}
