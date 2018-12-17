<?php

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
        Schema::connection(ConfigService::getDatabaseConnection())->create('myst_users', function (Blueprint $table) {
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
        Schema::connection(ConfigService::getDatabaseConnection())->dropIfExists('myst_users');
    }
}
