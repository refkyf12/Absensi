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
        Schema::create('akumulasi_tahunan', function (Blueprint $table) {
            $table->id();
            $table->integer('users_id');
            $table->string('nama');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('role')->onUpdate('cascade');
            $table->unsignedBigInteger('jam_lebih')->nullable();
            $table->unsignedBigInteger('jam_kurang')->nullable();
            $table->unsignedBigInteger('jam_lembur')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akumulasi_tahunans');
    }
};
