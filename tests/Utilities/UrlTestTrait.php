<?php

declare(strict_types=1);

namespace Tests\Utilities;

use App\Models\Url;

trait UrlTestTrait
{
    protected function createTestUrl(array $overrides = []): Url
    {
        return Url::factory()->create($overrides);
    }
}
