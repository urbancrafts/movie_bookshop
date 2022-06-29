<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('name')->nullable();
            //$table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('exchange_id')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('poster_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->integer('ng_wallet')->default(0);
            $table->string('password');
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_complete')->default(false);
            $table->boolean('is_bvn_verified')->default(false);
            $table->text('img_url')->nullable();
            $table->enum('status', ['Pending', 'Active','Blocked','Suspended','Deleted'])->default('Pending')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
