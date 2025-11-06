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
        'shortened_url',
        'analytics',
    ];

    protected $casts = [
        'analytics' => 'array',
    ];
}
