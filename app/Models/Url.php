<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $shortened)
 * @method static create(array $array)
 */
class Url extends Model
{
    protected $fillable = [
        'original_url',
        'target_url', // Alias for compatibility with service layer
        'shortened_url',
        'analytics',
        'clicks',
    ];

    protected $casts = [
        'analytics' => 'array',
    ];

    /**
     * Accessor for target_url to support both naming conventions
     */
    public function getTargetUrlAttribute(): ?string
    {
        return $this->attributes['original_url'] ?? $this->attributes['target_url'] ?? null;
    }

    /**
     * Mutator for target_url to map to original_url
     */
    public function setTargetUrlAttribute(?string $value): void
    {
        $this->attributes['original_url'] = $value;
    }
}
