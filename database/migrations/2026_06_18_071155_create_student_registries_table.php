<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_registries', function (Blueprint $table) {

            $table->id();

            $table->string('username', 100)->unique();

            $table->string('student_custom_id', 50);

            $table->string('institute_code', 20);

            $table->boolean('is_active')->default(true);

            $table->timestamp('last_login_at')->nullable();

            $table->timestamps();

            $table->index('institute_code');
            $table->index('student_custom_id');
            $table->index(['username', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_registries');
    }
};