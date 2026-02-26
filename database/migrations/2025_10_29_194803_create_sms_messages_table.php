<?php

declare(strict_types=1);

use App\Enums\Status;
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
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->longText('message');
            $table->string('receiver');
            $table->string('sender')->nullable();
            $table->decimal('cost', 19, 0)->default(0);
            $table->string('action')->nullable();
            $table->string('status')->default(Status::SENT->value);
            $table->longText('logs')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained('sms_drivers');
            $table->foreignId('pattern_id')->nullable()->constrained('sms_patterns');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
