<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->string('document_number', 30);
            $table->unique('document_number', 'unique_document_number');
            $table->string('password');
            $table->string('email')->nullable();
            $table->unique('email', 'unique_email');
            $table->string('name')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->boolean('banned')->default(false);
            $table->boolean('active')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}