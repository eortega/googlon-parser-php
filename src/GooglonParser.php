<?php

declare(strict_types=1);

namespace GooglonParser;

final class GooglonParser
{
    /**
     * Googlon letters are classified in two groups:
     * the letters u, d, x, s, m, p, f are called "foo letters"
     * while the other letters are called "bar letters".
     */
    private const FOO_LETTERS = ['u', 'd', 'x', 's', 'm', 'p', 'f'];

    /**
     * The first letter of the Googlon alphabet represents the digit 0,
     * the second letter represents the digit 1,
     * and the last one represents the digit 19.
     */
    private const LETTERS_WEIGHT = [
        's' => 0,
        'x' => 1,
        'o' => 2,
        'c' => 3,
        'q' => 4,
        'n' => 5,
        'm' => 6,
        'w' => 7,
        'p' => 8,
        'f' => 9,
        'y' => 10,
        'h' => 11,
        'e' => 12,
        'l' => 13,
        'j' => 14,
        'r' => 15,
        'd' => 16,
        'g' => 17,
        'u' => 18,
        'i' => 19,
    ];

    private const NUMBER_BASE = 20;

    /**
     * The prepositions are the words of:
     * - Exactly 6 letters
     * - End in a foo letter
     * - Do not contain the letter u.
     *
     * @param String $word
     */
    public static function isPreposition(string $word): bool
    {
        $wordLen = strlen($word);
        if ($wordLen !== 6) {
            return false;
        }

        if (strpos($word, 'u') !== false) {
            return false;
        }

        if (! in_array($word[$wordLen - 1], self::FOO_LETTERS, true)) {
            return false;
        }

        return true;
    }

    /**
     * @return array<string>
     */
    public static function tokenize(string $text): array
    {
        return explode(' ', $text);
    }

    /**
     * Verbs are words of
     *  - 6 letters or more
     *  - Ends in a bar letter.
     * Furthermore, if a verb starts in a bar letter,
     * then the verb is inflected in its subjunctive form.
     */
    public static function isVerb(string $word): bool
    {
        $wordLen = strlen($word);
        if ($wordLen < 6) {
            return false;
        }

        if (in_array($word[$wordLen - 1], self::FOO_LETTERS, true)) {
            return false;
        }

        return true;
    }

    /**
     * If a verb starts in a bar letter,
     * then the verb is inflected in its subjunctive form.
     *
     * @param String $word
     */
    public static function isSubjunctiveVerb(string $word): bool
    {
        if (! self::isVerb($word)) {
            return false;
        }

        if (in_array($word[0], self::FOO_LETTERS, true)) {
            return false;
        }

        return true;
    }

    /**
     * In Googlon, like in our system,
     * words are always ordered lexicographically,
     * but the challenge is that the order of the letters
     * in the Googlon alphabet is different from ours.
     * Their alphabet, in order, is: sxocqnmwpfyheljrdgui.
     *
     * @param array<string> $words
     *
     * @return array<string>
     */
    public static function lexicographicalSort(array $words): array
    {
        return self::radix($words);
    }

    /**
     * @param array<string> $words
     *
     * @return array<string>
     */
    public static function radix(array $words): array
    {
        $bucket = self::createBucket();
        $maxLength = self::getMaxLengthWord($words);
        $paddChar = 's';

        for ($i = $maxLength - 1; $i >= 0; $i--) {
            foreach ($words as $word) {
                $backfilledWord =
                    str_pad($word, $maxLength, $paddChar, STR_PAD_RIGHT);
                $bucket[$backfilledWord[$i]][] = $word;
            }

            //make $bucket as a flatten array after each iteration
            $words = self::flatBucket($bucket);
            $bucket = self::createBucket();
        }

        return $words;
    }

    /**
     * @param array<string, string> $bucket
     *
     * @return array<string>
     */
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
     * @param array<string> $words
     */
    public static function getMaxLengthWord(array $words): int
    {
        $largestWord = 0;

        foreach ($words as $word) {
            $largestWord = strlen($word) > $largestWord ?
                strlen($word) :
                $largestWord;
        }

        return $largestWord;
    }

    /**
     * Creates an empty bucket based on googlon alphabet
     *
     * @return array<null>
     */
    public static function createBucket(): array
    {
        return array_map(static fn (): array => [], self::LETTERS_WEIGHT);
    }

    /**
     * Googlons consider a number to be pretty
     * if it satisfies all of the following properties:
     *  - it is greater than or equal to 81827
     *  - it is divisible by 3
     */
    public static function isPrettyNumber(int $number): bool
    {
        return $number >= 81827 && ($number % 3) === 0;
    }

    /**
     * In Googlon, words also represent numbers given in base 20,
     * where each letter is a digit.
     * The digits are ordered
     * from the least significant to the most significant,
     * which is the opposite of our system.
     * That is,
     *  - The leftmost digit is the unit,
     *  - The second digit is worth 20,
     *  - The third one is worth 400,
     *  - and so on and so forth.
     * The values of the letters are given
     * by the order they appear in the Googlon alphabet
     * (which, as we saw, is ordered differently from our alphabet).
     */
    public static function wordToNumber(string $word): int
    {
        $value = 0;
        $wordLen = strlen($word);
        $base = 1;

        for ($index = 0; $index < $wordLen; $index++) {
            $value += self::LETTERS_WEIGHT[$word[$index]] * $base;
            $base *= self::NUMBER_BASE;
        }
        return $value;
    }
}
