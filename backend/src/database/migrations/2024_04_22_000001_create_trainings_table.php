<?php

use App\Core\Database\Migration;

class CreateTrainingsTable extends Migration
{
    public function up()
    {
        $this->schema->create('trainings', function ($table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location')->nullable();
            $table->string('trainer')->nullable();
            $table->integer('max_participants')->nullable();
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('trainings');
    }
} 