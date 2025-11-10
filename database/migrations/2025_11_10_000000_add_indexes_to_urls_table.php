<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToUrlsTable extends Migration
{
    public function up(): void
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->index('original_url', 'idx_urls_original_url');
            $table->index('clicks', 'idx_urls_clicks');
            $table->index('created_at', 'idx_urls_created_at');
            $table->index(['shortened_url', 'created_at'], 'idx_urls_shortened_created');
        });
    }

    public function down(): void
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->dropIndex('idx_urls_original_url');
            $table->dropIndex('idx_urls_clicks');
            $table->dropIndex('idx_urls_created_at');
            $table->dropIndex('idx_urls_shortened_created');
        });
    }
}
