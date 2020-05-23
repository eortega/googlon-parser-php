<?php
require_once 'src/GooglonParser.php';

class GooglonParserTest
{

    protected $testCases = null;

    public function start()
    {
        $this->loadCases();

        foreach($this->testCases as $caseNum=>$text) {
            $this->executeTestCase($caseNum, $text);
        }

    }

    protected function executeTestCase(string $caseNumber, array $testCase)
    {
        echo "Case {$caseNumber} \n";
        $tokens = GooglonParser::tokenize($testCase['text']);

        // PREPOSOTIONS
        $prepositions = array_filter($tokens,'GooglonParser::isPreposition');
        $this->assertIntWithMessage('Prepositions',$testCase['e']['propositions'],count($prepositions));

        //VERBS
        $verbs = array_filter($tokens,'GooglonParser::isVerb');
        $this->assertIntWithMessage('Verbs',$testCase['e']['verbs'],count($verbs));

        // SUBJUNCTIVE VERBS
        $subjVerbs = array_filter($tokens,'GooglonParser::isSubjunctiveVerb');
        $this->assertIntWithMessage('Subjunctive Verbs',$testCase['e']['subjVerbs'],count($subjVerbs));

        //PRETTY NUMBERS
        $numbersList = array_map('GooglonParser::wordToNumber', $tokens);
        $prettyNumbers = array_filter($numbersList,'GooglonParser::isPrettyNumber');
        $this->assertIntWithMessage('Unique Pretty Numbers',$testCase['e']['distinctPrettyNumbers'],count(array_unique($prettyNumbers)));


        //$vocabularyList = GooglonParser::lexicographicalSort(array_unique($tokens));
        //echo sprintf("There are %s Propoistions \n", count($prepositions));
        $lexicallyOrdered = GooglonParser::radix(array_unique($tokens));
        $this->assertSortedList('Vocabulary List',$testCase['e']['sorted'], $lexicallyOrdered);
        //$this->printLists($testCase['e']['sorted'], $vocabularyList);
        echo "\n\n";
    }

    protected function assertIntWithMessage(string $message, int $expected, int $real)
    {
        $text = sprintf("%s: Expected %s and Received %s\n", $message, $expected, $real);
        //Red by default
        $cliColor = "\e[0;31;m%s\e[0m";
        if($expected == $real) {
            $cliColor = "\e[0;32;m%s\e[0m";
        }

        echo sprintf($cliColor, $text);
    }

    protected function printLists(array $expected, array $real)
    {
        $m = count($expected) >= count($real) ? count($expected) : count($real);
        echo "E\t | R\n";
        for($i=0; $i<$m; $i++) {
            $ev = $expected[$i] ?? '_';
            $rv = $real[$i] ?? '_';
            echo sprintf("%s \t\t\t| %s\n",$ev, $rv);
        }

    }

    protected function assertSortedList(string $message, array $expected, array $real)
    {
        $listsSizeMessage = sprintf("%s: Expected %s and Received %s\n", $message, count($expected), count($real));
        $listSortMessage = sprintf("%s: Sorted\n", $message);
        //Red by default
        $cliColorListSize = "\e[0;31;m%s\e[0m";
        $cliColorListSorted = "\e[0;32;m%s\e[0m";

        //Verify List size
        if(count($expected) == count($real)) {
            $cliColorListSize = "\e[0;32;m%s\e[0m";

            for($i=0; $i< count($expected); $i++) {
                if($expected[$i] != $real[$i]) {
                    $cliColorListSorted = "\e[0;31;m%s\e[0m";
                    echo "$expected[$i]:: $real[$i]\n";
                    //$sorted=false;
                    break;
                }
            }

        }

        echo sprintf($cliColorListSize, $listsSizeMessage);
        echo sprintf($cliColorListSorted, $listSortMessage);
        //echo sprintf($cliColorListSIze, $itemNumber);

    }

    protected function loadCases()
    {
        $this->testCases = include 'TestCases.php';
    }
}


$t = new GooglonParserTest();
$t->start();
