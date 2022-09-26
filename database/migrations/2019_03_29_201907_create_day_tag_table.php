<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_tag', function (Blueprint $table) {
            $table->foreignId('day_id');
            $table->foreignId('tag_id');
            $table->timestamps();

            $table->primary(['day_id', 'tag_id']);

            $table->foreign('day_id')->references('id')->on('days')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('tag_id')->references('id')->on('tags')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('day_tag');
    }
};
