<?php

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
        Schema::connection(ConfigService::getDatabaseConnection())->create('myst_restrictions', function (Blueprint $table) {
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
        Schema::connection(ConfigService::getDatabaseConnection())->dropIfExists('myst_restrictions');
    }
}
