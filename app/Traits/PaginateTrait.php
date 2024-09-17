<?php

namespace App\Traits;

trait PaginateTrait
{
    const MAX_PER_PAGE = 100;

    const DEFAULT_PER_PAGE = 10;

    public function getPerPage(?int $perPage = null): int
    {
        if (! $perPage) {
            $perPage = (int) request()->get('per_page');
        }

        if (! is_int($perPage)) {
            return self::DEFAULT_PER_PAGE;
        }

        return ($perPage > self::MAX_PER_PAGE) ? self::MAX_PER_PAGE : ($perPage < 1 ? self::DEFAULT_PER_PAGE : $perPage);
    }
}
