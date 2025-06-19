<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('from_city_id');
            $table->string('to_city_id');
            $table->string('weight')->nullable();
            $table->string('price')->nullable();
            $table->date('preferred_date')->nullable();
            $table->date('delivery_deadline')->nullable();
            $table->string('status')->default('pending');
            $table->text('note')->nullable();
            $table->tinyInteger('for_self')->default(1);
            $table->string('receiver_name')->nullable();
            $table->string('receiver_number')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_requests');
    }
};
