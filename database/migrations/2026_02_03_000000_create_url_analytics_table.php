<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('url_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('url_id');
            $table->string('referer_domain')->nullable();
            $table->string('user_agent_family')->nullable();
            $table->timestamp('created_at')->nullable();
            
            $table->foreign('url_id')->references('id')->on('urls')->onDelete('cascade');
            $table->index('url_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_analytics');
    }
};