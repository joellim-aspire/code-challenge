<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('loan_term');
            $table->integer('loan_term_remaining');
            $table->decimal('amount_required');
            $table->decimal('amount_balance');
            $table->date('loan_start_date');
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Paid'])->default('Pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
};
