<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConcertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concerts', function (Blueprint $table) {
            $table->id();
            $table->string('title',50);
            $table->string('subtitle',50);
            $table->dateTime('date');
            $table->integer('ticket_price');
            $table->string('venu',30);
            $table->string('venu_address',30);
            $table->string('city',20);
            $table->string('state',20);
            $table->string('zip',20);
            $table->string('additional',100);
            $table->dateTime('published_at')->nullable();
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
        Schema::dropIfExists('concerts');
    }
}
