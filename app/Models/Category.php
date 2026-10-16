<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    const GREAT = 1;
    const GOOD = 2;
    const AVERAGE = 3;
    const BAD = 4;
    const WORST = 5;

    protected $fillable = [
        'name',
        'color'
    ];

    public function days(): HasMany
    {
        return $this->hasMany(Day::class);
    }

    /**
     * A word for the mood, for screen readers; the name is just a smiley.
     */
    public function label(): string
    {
        return match ($this->order) {
            self::GREAT => 'Great',
            self::GOOD => 'Good',
            self::AVERAGE => 'Average',
            self::BAD => 'Bad',
            self::WORST => 'Worst',
            default => 'Mood',
        };
    }
}
