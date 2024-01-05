<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            $table->string('username');
            $table->string('password');
            $table->string('email')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('role_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Add Admin user
        DB::table('users')->insert([
            'username'   => 'admin',
            'password'   => Hash::make('123456'),
            'email'      => 'admin@gmail.com',
            'first_name' => 'Jose',
            'last_name'  => 'Rizal',
            'created_at'  => Carbon\Carbon::now(),
            'updated_at'  => Carbon\Carbon::now(),
        ]);
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
