<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tasks', function (Blueprint $table) {
           $table->integer('id')->autoIncrement();
            $table->string('title');
            $table->integer('asigned_to');
            $table->string('status')->default('assigned');
            $table->integer('assigned_by');
            $table->string('description');
            $table->string('deleted_by')->nullable(); // default
            $table->string('assigned_by_name');
            $table->string('asigned_to_name');
            $table->dateTime('due_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
