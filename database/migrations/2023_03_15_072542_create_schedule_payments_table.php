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
        Schema::create(
            'schedule_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('loan_id');
                $table->enum('status', ['PENDING','APPROVED','PAID'])->default('PENDING');
                $table->dateTime('due_date');
                $table->decimal('amount', 11, 2);
                $table->decimal('customer_input_amount', 11, 2)->nullable();
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_payments');
    }
};
