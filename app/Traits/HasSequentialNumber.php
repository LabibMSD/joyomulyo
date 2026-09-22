<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * @method static \Illuminate\Database\Eloquent\Builder query()
 * @method static \Illuminate\Database\Eloquent\Builder withTrashed()
 */
trait HasSequentialNumber
{
    protected static function generateSequentialNumber(string $column, string $prefix, int $padLength = 6): string
    {
        return DB::transaction(function () use ($column, $prefix, $padLength) {
            $query = static::query();

            if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
                $query->withTrashed();
            }

            $last = $query
                ->where($column, 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc($column)
                ->value($column);

            $next = $last ? ((int) substr($last, -$padLength)) + 1 : 1;

            return $prefix.str_pad((string) $next, $padLength, '0', STR_PAD_LEFT);
        });
    }
}
