<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Blaze\Myst\Services\ConfigService;

class CreateMystUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('myst_users', function (Blueprint $table) {
            $table->integer('id');
            $table->primary('id');
            $table->boolean('is_bot');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->string('language_code')->nullable();
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
        Schema::dropIfExists('myst_users');
    }
    
    /**
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public function getConnection()
    {
        return ConfigService::getDatabaseConnection();
    }
}
