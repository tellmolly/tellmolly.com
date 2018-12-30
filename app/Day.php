<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Day extends Model
{
    protected $fillable = [
        'date',
        'comment'
    ];

    /**
     * Returns all days from the given year.
     *
     * @param Builder $query
     * @param int     $year
     *
     * @return Builder
     */
    public function scopeYear(Builder $query, $year)
    {
        return $query->whereYear('date', '=', $year);
    }

    /**
     * Get the category that the day belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
