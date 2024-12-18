<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UniqueTranslation implements ValidationRule
{
    protected Model $parent;

    protected bool $isCreate;

    protected string $locale;

    protected string $column;

    public function __construct(bool $isCreate, Model $parent, string $locale, string $column = 'title')
    {
        $this->isCreate = $isCreate;
        $this->parent = $parent;
        $this->locale = $locale;
        $this->column = $column;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @phpstan-ignore-next-line */
        $existed = DB::table($this->parent->getTranslationTable())
            ->where('locale', $this->locale)
            ->when(! $this->isCreate, function ($query) {
                /** @phpstan-ignore-next-line */
                $query->whereNot('item_id', $this->parent->id);
            })
            ->where($this->column, $value)
            ->count();
        if ($existed) {
            $fail(__('validation.unique'));
        }
    }
}
