<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_panels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('status')->nullable()->default(true);
            $table->integer('order')->nullable()->default(1);
            $table->timestamps();
        });

        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('panel_id')->unsigned()->nullable()->default(null);
            $table->foreign('panel_id')->references('id')->on('content_panels')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('order')->nullable()->default(1);
            $table->bigInteger('article_id')->nullable();
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
        Schema::dropIfExists('contents');
        Schema::dropIfExists('content_panels');
    }
}
