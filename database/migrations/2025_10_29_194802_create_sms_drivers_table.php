<?php

declare(strict_types=1);

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
        Schema::create('sms_drivers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('api');
            $table->string('sender')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->boolean('allow_single')->default(true);
            $table->boolean('allow_bulk')->default(true);
            $table->boolean('allow_pattern')->default(true);
            $table->boolean('is_default')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_drivers');
    }
};
