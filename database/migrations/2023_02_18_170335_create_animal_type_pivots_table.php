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
        Schema::create('animal_types_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')
                ->constrained('animals')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('type_id')
                ->constrained('animal_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
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
        Schema::dropIfExists('animal_type_pivots');
    }
};
