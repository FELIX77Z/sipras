<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user');
            }

            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('aktif');
            }

            if (! Schema::hasColumn('users', 'nis')) {
                $table->string('nis')->nullable();
            }

            if (! Schema::hasColumn('users', 'nisn')) {
                $table->string('nisn')->nullable();
            }

            if (! Schema::hasColumn('users', 'nip')) {
                $table->string('nip')->nullable();
            }

            if (! Schema::hasColumn('users', 'class')) {
                $table->string('class')->nullable();
            }

            if (! Schema::hasColumn('users', 'major')) {
                $table->string('major')->nullable();
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'nis', 'nisn', 'nip', 'class', 'major', 'phone']);
        });
    }
};
