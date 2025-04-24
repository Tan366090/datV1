<?php

use App\Core\Database\Migration;

class CreateTrainingRegistrationsTable extends Migration
{
    public function up()
    {
        $this->schema->create('training_registrations', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('training_id')->unsigned();
            $table->dateTime('registration_date');
            $table->enum('status', ['registered', 'attended', 'cancelled'])->default('registered');
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('training_id')->references('id')->on('trainings');
        });
    }

    public function down()
    {
        $this->schema->drop('training_registrations');
    }
} 