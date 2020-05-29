<?php

declare(strict_types=1);

namespace eortega\GooglonParser\Algorithms\Sort;

final class Radix implements SortAlgorithm
{
    /**
     * @var array<string> $list
     */
    private array $items;

    /**
     * @var array<string, int> $list
     */
    private array $alphabet;

    private string $padString;

    /**
     * Radix constructor.
     *
     * @param array<string> $items
     * @param array<string> $alphabet
     */
    public function __construct(
        array $items,
        array $alphabet,
        string $padString
    ) {
        $this->items = $items;
        $this->alphabet = $alphabet;
        $this->padString = $padString;
    }

    /**
     * @param array<string> $list
     *
     * @return array<string>
     */
    public function sort(): array
    {
        $words = $this->items;
        $bucket = $this->createBucket();
        $maxLength = $this->getLengthOfLargestItem();

        for ($i = $maxLength - 1; $i >= 0; $i--) {
            foreach ($words as $word) {
                $backfilledWord =
                    str_pad($word, $maxLength, $this->padString, STR_PAD_RIGHT);
                $bucket[$backfilledWord[$i]][] = $word;
            }

            //make $bucket as a flatten array after each iteration
            $words = $this->flatBucket($bucket);
            $bucket = $this->createBucket();
        }

        return $words;
    }

    private function getLengthOfLargestItem(): int
    {
        $largest = 0;

        foreach ($this->items as $item) {
            $itemLength = strlen($item);
            $largest = $itemLength > $largest ?
                $itemLength :
                $largest;
        }

        return $largest;
    }

    /**
     * Creates an empty bucket based on googlon alphabet
     *
     * @return array<null>
     */
    private function createBucket(): array
    {
        return array_map(static fn (): array => [], $this->alphabet);
    }

    /**
     * @param array<string, string> $bucket
     *
     * @return array<string>
     */
    private function flatBucket(array $bucket): array
    {
        return array_reduce($bucket, static function ($carry, $item) {
            $carry = $carry ?? [];
            return array_merge($carry, $item);
        });
    }
}
