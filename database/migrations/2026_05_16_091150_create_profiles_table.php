<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->after('password');
            $table->string('student_id')->nullable()->after('role');
            $table->string('course')->nullable()->after('student_id');
            $table->unsignedTinyInteger('year_level')->nullable()->after('course');
            $table->text('bio')->nullable()->after('year_level');
            $table->timestamp('profile_completed_at')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'student_id',
                'course',
                'year_level',
                'bio',
                'profile_completed_at',
            ]);
        });
    }
};