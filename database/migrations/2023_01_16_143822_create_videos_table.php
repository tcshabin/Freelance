<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // "viewCount" => "2"
        // "likeCount" => "2"
        // "favoriteCount" => "0"
        // "commentCount" => "0"
        Schema::table('videos', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('video_id')->nullable();
            $table->string('channel_id')->nullable();
            $table->string('view_count')->nullable();
            $table->string('like_count')->nullable();
            $table->string('watch_time')->nullable();
            $table->string('type')->nullable();
            $table->longText('video_response')->nullable();
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
        Schema::table('videos', function (Blueprint $table) {
            //
        });
    }
}
