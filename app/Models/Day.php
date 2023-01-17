<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'comment',
        'grateful_for',
        'category_id'
    ];

    public function toExport(): array
    {
        return [
            'date' => $this->date,
            'category' => $this->category->order,
            'comment' => $this->comment,
            'grateful_for' => $this->grateful_for,
            'tags' => $this->tags->map(function (Tag $tag) {
                return $tag->name;
            })->implode(', '),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function scopeSearch(Builder $query, ?string $searchTerm): Builder
    {
        if ( ! $searchTerm) {
            return $query;
        }

        return $query->where('comment', 'LIKE', '%' . $searchTerm . '%');
    }

    public function scopeYear(Builder $query, int $year): Builder
    {
        return $query->whereYear('date', '=', $year);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function resolveRouteBinding($value, $field = null): Model|null
    {
        return $this
            ->where('user_id', auth()->id())
            ->where($this->getRouteKeyName(), $value)
            ->firstOrFail();
    }

    public function getRouteKeyName(): string
    {
        return 'date';
    }
}
