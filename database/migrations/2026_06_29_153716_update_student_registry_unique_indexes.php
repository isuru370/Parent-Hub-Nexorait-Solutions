<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_registries', function (Blueprint $table) {

            // Remove old unique index
            $table->dropUnique(['username']);

            // Add new composite unique indexes
            $table->unique(
                ['institute_code', 'username'],
                'student_registry_institute_username_unique'
            );

            $table->unique(
                ['institute_code', 'student_custom_id'],
                'student_registry_institute_student_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('student_registries', function (Blueprint $table) {

            $table->dropUnique(
                'student_registry_institute_username_unique'
            );

            $table->dropUnique(
                'student_registry_institute_student_unique'
            );

            $table->unique('username');

            // Remove this line if student_custom_id
            // was not unique before.
            $table->dropUnique([
                'student_custom_id'
            ]);
        });
    }
};