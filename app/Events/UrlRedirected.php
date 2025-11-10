<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Url;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UrlRedirected
{
    use Dispatchable, SerializesModels;

    public Url $url;

    public array $meta;

    public function __construct(Url $url, array $meta = [])
    {
        $this->url = $url;
        $this->meta = $meta;
    }
}
