<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYoutubeReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtube_reports', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('channel_id');
            $table->date('date')->nullable();
            $table->integer('views')->nullable();
            $table->integer('likes')->nullable();
            $table->integer('sub_get')->nullable();
            $table->longText('response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youtube_reports');
    }
}
