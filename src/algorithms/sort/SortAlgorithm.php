<?php

declare(strict_types=1);

namespace eortega\GooglonParser\Algorithms\Sort;

interface SortAlgorithm
{
    /**
     * @return array<mixed>
     */
    public function sort(): array;
}
