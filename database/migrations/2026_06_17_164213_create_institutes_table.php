<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutes', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->string('code')->unique();

            $table->string('api_url');

            $table->string('logo')->nullable();

            $table->string('contact_number')->nullable();

            $table->string('contact_email')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamp('last_ping_at')->nullable();

            $table->string('app_version')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutes');
    }
};