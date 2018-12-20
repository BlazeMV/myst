<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Blaze\Myst\Services\ConfigService;

class CreateMystChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('myst_chats', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->primary('id');
            $table->string('type');
            $table->string('title')->nullable();
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->boolean('all_members_are_administrators')->nullable();
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
        Schema::dropIfExists('myst_chats');
    }
    
    /**
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public function getConnection()
    {
        return ConfigService::getDatabaseConnection();
    }
}
