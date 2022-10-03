<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlinePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_ref_id', 20);
            $table->string('policy_no', 14);
            $table->integer('amount');
            $table->string('status', 10);
            $table->string('ref_mobile_no', 14); 
            $table->ipAddress('client_ip'); 
            $table->string('client_id', 14);
            $table->string('client_name', 20);          
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_payments');
    }
}
