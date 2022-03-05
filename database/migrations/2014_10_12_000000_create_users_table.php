<?php

use App\Entities\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('permissions')->default(User::PERMISSION_CLIENT);
            $table->string('phone_numbers')->nullable();

            $table->string('address_street_name')->nullable();
            $table->string('address_neighborhood')->nullable();
            $table->string('address_number')->nullable();
            $table->string('address_district', 2)->nullable();
            $table->string('address_city', 2)->nullable();
            $table->string('address_postal_code', 8)->nullable();
            $table->decimal('adress_longitude', 10, 7)->nullable();
            $table->decimal('address_latitude', 10, 7)->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
};
