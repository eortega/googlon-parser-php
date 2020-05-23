<?php
declare(strict_types=1);
namespace GooglonParser;

class GooglonParser
{
    /**
     * Googlon letters are classified in two groups:
     * the letters u, d, x, s, m, p, f are called "foo letters"
     * while the other letters are called "bar letters".
     */
    const FOO_LETTERS = ['u', 'd', 'x', 's', 'm', 'p', 'f'];

    /**
     * The first letter of the Googlon alphabet represents the digit 0, the second letter represents the digit 1,
     * and the last one represents the digit 19.
     */
    const LETTERS_WEIGHT = [
        's' =>0,
        'x' =>1,
        'o' =>2,
        'c' =>3,
        'q' =>4,
        'n' =>5,
        'm' =>6,
        'w' =>7,
        'p' =>8,
        'f' =>9,
        'y' =>10,
        'h' =>11,
        'e' =>12,
        'l' =>13,
        'j' =>14,
        'r' =>15,
        'd' =>16,
        'g' =>17,
        'u' =>18,
        'i' =>19
    ];

    const NUMBER_BASE = 20;

    public static function tokenize(String $text): array
    {
        return explode(' ', $text);
    }

    /**
     * The prepositions are the words of exactly 6 letters which end in a foo letter and do not contain the letter u.
     *
     * @param String $w
     * @return bool
     */
    public static function isPreposition(String $w): bool
    {
        $t = str_split($w);
        if ((count($t) == 6) && (!in_array('u', $t)) && in_array($t[count($t)-1], self::FOO_LETTERS)) {
            return true;
        }

        return false;
    }

    /**
     * Verbs are words of 6 letters or more that end in a bar letter.
     * Furthermore, if a verb starts in a bar letter, then the verb is inflected in its subjunctive form.
     * @param String $w
     * @return bool
     */
    public static function isVerb(String $w): bool
    {
        $t = str_split($w);
        if ((count($t) >= 6) && !in_array($t[count($t)-1], self::FOO_LETTERS)) {
            return true;
        }

        return false;
    }


    /**
     * Furthermore, if a verb starts in a bar letter, then the verb is inflected in its  subjunctive form.
     *
     * @param String $w
     * @return bool
     */
    public static function isSubjunctiveVerb(String $w): bool
    {
        if (self::isVerb($w)) {
            $t = str_split($w);
            if (!in_array($t[0], self::FOO_LETTERS)) {
                return true;
            }
        }

        return false;
    }


    /**
     * In Googlon, like in our system, words are always ordered lexicographically,
     * but the challenge is that the order of the letters in the Googlon alphabet is different from ours.
     * Their alphabet, in order, is: sxocqnmwpfyheljrdgui.
     * @param array $words
     * @return array
     */
    public static function lexicographicalSort(array $words): array
    {
        return self::radix($words);
    }

    /**
     *
     * @param array $words
     * @return array
     */
    public static function radix(array $words)
    {
        $bucket = self::createBucket();
        $maxLength = self::getMaxLengthWord($words);
        $paddChar='s';

        for ($i = $maxLength -1; $i>=0; $i--) {
            foreach ($words as $w) {
                $padded = str_pad($w, $maxLength, $paddChar, STR_PAD_RIGHT);
                $tw = str_split($padded);
                $bucket[$tw[$i]][] = $w;
            }

            //make $bucket as a flatten array after each iteration
            $words=self::flatBucket($bucket);
            $bucket = self::createBucket();
        }

        return $words;
    }

    public static function flatBucket(array $bucket): array
    {
        $list = [];
        foreach ($bucket as $letter => $wordsInLetterBucket) {
            foreach ($wordsInLetterBucket as $word) {
                $list[] = $word;
            }
        }

        return $list;
    }

    /**
     * @param array $words
     * @return int
     */
    public static function getMaxLengthWord(array $words): int
    {
        $largestWord=0;

        foreach ($words as $word) {
            $largestWord = strlen($word) > $largestWord ? strlen($word) : $largestWord;
        }

        return $largestWord;
    }

    /**
     * Creates an empty bucket based on googlon alphabet
     * @return array
     */
    public static function createBucket(): array
    {
        return array_map(static function ($k) {
            return [];
        }, self::LETTERS_WEIGHT);
    }

    /**
     * Googlons consider a number to be pretty if it satisfies all of the following properties:
     *   it is greater than or equal to 81827
     *   it is divisible by 3
     *
     * @param int $n
     * @return bool
     */
    public static function isPrettyNumber(int $n): bool
    {
        return (($n>=81827) && (($n % 3) == 0));
    }

    /**
     * In Googlon, words also represent numbers given in base 20, where each letter is a digit.
     * The digits are ordered from the least significant to the most significant, which is the opposite of our system.
     * That is,
     *  - The leftmost digit is the unit,
     *  - The second digit is worth 20,
     *  - The third one is worth 400,
     *  - and so on and so forth.
     * The values of the letters are given by the order they appear in the Googlon alphabet
     * (which, as we saw, is ordered differently from our alphabet).
     *
     * @param String $w
     * @return int
     */
    public static function wordToNumber(String $w): int
    {
        $t = str_split($w);
        $value=0;
        $tCount = count($t);
        for ($i=0,$j=1; $i<$tCount; $i++, $j*=self::NUMBER_BASE) {
            $value+=self::LETTERS_WEIGHT[$t[$i]] * $j;
        }

        return $value;
    }
}
