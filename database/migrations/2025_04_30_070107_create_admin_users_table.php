<?php

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
       Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('userName', 30);
            $table->string('password');
            $table->unsignedBigInteger('adminRole');
            $table->boolean('isActive')->default(true);
            $table->timestamps();

            $table->foreign('adminRole')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
