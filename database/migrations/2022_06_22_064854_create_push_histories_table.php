<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('oauth_clients')->cascadeOnDelete();
            $table->foreignId('push_id')->constrained('pushes')->cascadeOnDelete();
            $table->string('token')->comment('fcm token');
            $table->unsignedSmallInteger('status')->comment('status code');
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
        Schema::dropIfExists('push_histories');
    }
}
