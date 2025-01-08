<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('subject');
            $table->float('predicted_score');
            $table->string('quarter'); // e.g., Q1, Q2, Q3, Q4
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('predictions');
    }
};

