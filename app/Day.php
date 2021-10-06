<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'comment'
    ];

    public function scopeSearch(Builder $query, ?string $searchTerm): Builder
    {
        if ( ! $searchTerm) {
            return $query;
        }

        return $query->where('comment', 'LIKE', '%' . $searchTerm . '%');
    }

    /**
     * Returns all days from the given year.
     *
     * @param Builder $query
     * @param int $year
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

    /**
     * Get the tags that belong to the day.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
