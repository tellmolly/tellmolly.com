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

    /**
     * Longest comment, in characters. The column is a TEXT (65,535 bytes) and a
     * character takes up to 4 bytes in utf8mb4, so this always fits.
     */
    public const COMMENT_MAX = 16000;

    /** Matches the VARCHAR(255) column. */
    public const GRATEFUL_FOR_MAX = 255;

    protected $fillable = [
        'date',
        'comment',
        'grateful_for',
        'category_id'
    ];

    /**
     * A gratitude prompt for this day. Chosen from the date, so it stays the
     * same while the day is being edited and changes from one day to the next.
     */
    public function gratitudePrompt(): string
    {
        $prompts = config('gratitude.prompts');

        return $prompts[crc32((string) $this->date) % count($prompts)];
    }

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

        $term = '%' . $searchTerm . '%';

        // Grouped so the ORs can't escape the user scope the query starts with.
        return $query->where(function (Builder $query) use ($term) {
            $query->where('comment', 'LIKE', $term)
                ->orWhere('grateful_for', 'LIKE', $term)
                ->orWhereHas('tags', fn(Builder $query) => $query->where('name', 'LIKE', $term));
        });
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
