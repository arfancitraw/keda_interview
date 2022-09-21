<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransMessageAndReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trans_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('sender_id')->unsigned();
            $table->integer('receiver_id')->unsigned();
            $table->longText('message');
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('receiver_id')->references('id')->on('users');
        });

        Schema::create('trans_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('reporter_id')->unsigned();
            $table->integer('customer_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->tinyInteger('type')->comment('0: Feedback; 1: Bug');
            $table->longText('report');
            $table->timestamps();

            $table->foreign('reporter_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('staff_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trans_reports');
        Schema::dropIfExists('trans_messages');
    }
}
