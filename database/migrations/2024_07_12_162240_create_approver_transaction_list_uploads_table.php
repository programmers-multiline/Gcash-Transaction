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
        Schema::create('approver_transaction_list_uploads', function (Blueprint $table) {
            $table->id();
            $table->integer('approver_transaction_id');
            $table->integer('approver_transaction_number');
            $table->string('mobile_number');
            $table->string('client_name');
            $table->integer('amount');
            $table->string('remarks');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approver_transaction_list_uploads');
    }
};