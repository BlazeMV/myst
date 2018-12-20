<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Blaze\Myst\Services\ConfigService;

class CreateMystRestrictionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('myst_restrictions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('relative_id');
            $table->string('relative_type');
            $table->string('reason');
            $table->boolean('respond')->default(0);
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
        Schema::dropIfExists('myst_restrictions');
    }
    
    /**
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public function getConnection()
    {
        return ConfigService::getDatabaseConnection();
    }
}
