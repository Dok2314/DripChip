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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->float('weight');
            $table->float('length');
            $table->float('height');
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER']);
            $table->enum('lifeStatus', ['ALIVE', 'DEAD'])->default('ALIVE');
            $table->foreignId('chipperId')
                ->constrained('accounts')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->dateTime('chippingDateTime');
            $table->dateTime('deathDateTime')->default(null);
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
        Schema::dropIfExists('animals');
    }
};
