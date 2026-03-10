<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_users', function (Blueprint $table) {
            $table->integer('user_id')->primary(); // Note: Not auto-incrementing based on your SQL
            $table->string('name');
            $table->string('email');
            $table->string('password'); 
            $table->string('user_type');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_users');
    }
};