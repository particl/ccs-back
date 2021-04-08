<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('block_height_start')->unique();
            $table->unsignedInteger('block_height_end')->unique();
            $table->unsignedInteger('finished')->default(0);
            $table->unsignedInteger('blocks_yes');
            $table->unsignedInteger('blocks_no');
            $table->unsignedInteger('blocks_abstain');
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
        Schema::dropIfExists('votes');
    }
}
