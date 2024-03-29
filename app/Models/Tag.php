<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'color'
    ];

    public function fontColor(string $mode = "hsp"): string
    {
        $color = $this->color;

        if (str_starts_with($color, '#')) {
            $color = substr($color, 1);
        }

        // Convert to RGB value
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));

        if ($mode === "yiq") {
            return $this->fontColorYIQ($r, $g, $b);
        }

        return $this->fontColorHSP($r, $g, $b);
    }

    protected function fontColorYIQ(int $r, int $g, int $b): string
    {
        // Get YIQ ratio
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return '#' . (($yiq > 128) ? "000000" : "ffffff");
    }

    protected function fontColorHSP(int $r, int $g, int $b): string
    {
        $pRed = 0.299;
        $pGreen = 0.587;
        $pBlue = 0.114;

        // Get HSP ratio
        $yiq = sqrt(
            ($pRed * ($r / 255)) ** 2 +
            ($pGreen * ($g / 255)) ** 2 +
            ($pBlue * ($b / 255)) ** 2
        );

        return '#' . (($yiq > 0.5) ? "000000" : "ffffff");
    }

    public function toExport(): array
    {
        return [
            'name' => $this->name,
            'color' => $this->color,
        ];
    }

    public function scopeSortBy(Builder $query, string $sortOrder): Builder
    {
        [$column, $direction] = match ($sortOrder) {
            default => ['name', 'asc'],
            'z-a' => ['name', 'desc'],
            'uses-asc' => ['days_count', 'asc'],
            'uses-desc' => ['days_count', 'desc'],
        };

        return $query->orderBy($column, $direction);
    }

    public function days(): BelongsToMany
    {
        return $this->belongsToMany(Day::class);
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
        return 'slug';
    }

    protected static function booted()
    {
        self::creating(function (Tag $tag) {
            if ( ! isset($tag->slug)) {
                $count = 1;
                do {
                    $tag->slug = Str::slug($tag->name) . ($count > 1 ? '-' . $count : '');

                    $count++;
                } while (
                    Tag::query()
                        ->where('user_id', '=', $tag->user_id)
                        ->where('slug', $tag->slug)
                        ->exists()
                );
            }
        });
    }
}
