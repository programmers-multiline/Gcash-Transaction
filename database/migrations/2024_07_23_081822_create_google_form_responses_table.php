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
        Schema::create('google_form_responses', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->string('branch_name');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('mi');
            $table->string('suffix')->nullable();
            $table->string('pension_number');
            $table->string('pension_type');
            $table->integer('gcash_number');
            $table->integer('gcash_name');
            $table->date('date_created');
            $table->integer('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_form_responses');
    }
};
