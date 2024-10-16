<?php

namespace App\Traits;

trait PaginateTrait
{
    const MAX_PER_PAGE = 100;

    const DEFAULT_PER_PAGE = 10;

    public function getPerPage(?int $perPage = null, ?int $maxPerPage = null): int
    {
        $maxPerPage = $maxPerPage ?? self::MAX_PER_PAGE;
        if (! $perPage) {
            $perPage = (int) request()->get('per_page');
        }

        if (! is_int($perPage)) {
            return self::DEFAULT_PER_PAGE;
        }

        return ($perPage > $maxPerPage) ? $maxPerPage : ($perPage < 1 ? self::DEFAULT_PER_PAGE : $perPage);
    }
}
