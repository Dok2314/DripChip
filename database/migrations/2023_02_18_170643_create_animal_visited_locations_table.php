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
        Schema::create('animal_visited_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')
                ->constrained('animals')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('location_point_id')
                ->constrained('location_points')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->dateTime('startDateTime');
            $table->dateTime('endDateTime');
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
        Schema::dropIfExists('animal_visited_locations');
    }
};
