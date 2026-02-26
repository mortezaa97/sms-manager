<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_blacklists', function (Blueprint $table) {
            $table->id();
            $table->string('cellphone');
            $table->string('reason')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->dateTime('expire_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('cellphone');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_blacklists');
    }
};
