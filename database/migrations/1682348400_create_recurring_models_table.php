<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('repetitions')) {
            return;
        }

        Schema::create('repetitions', function (Blueprint $table) {
            $table->id();
            $table->morphs('repeatable');
            $table->enum('type', ['simple', 'complex'])->default('simple');
            $table->timestamp('start_at');
            $table->integer('interval')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->string('day')->nullable();
            $table->string('week')->nullable();
            $table->string('weekday')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repetitions');
    }
};
