<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Blaze\Myst\Services\ConfigService;

class CreateMystChatMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('myst_chat_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('myst_users')->onDelete('cascade');
            $table->integer('chat_id');
            $table->foreign('chat_id')->references('id')->on('myst_chats')->onDelete('cascade');
            $table->boolean('admin')->default(0);
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
        Schema::dropIfExists('myst_chat_members');
    }
    
    /**
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public function getConnection()
    {
        return ConfigService::getDatabaseConnection();
    }
}
