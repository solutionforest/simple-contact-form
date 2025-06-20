<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('simple_contact_form_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->text('content');
            $table->string('to');
            $table->text('email_body');
            $table->text('success_message');
            $table->text('error_message');
            $table->text('extra_attributes')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('simple_contact_form_table');
    }
};
