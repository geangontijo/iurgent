<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('details')->nullable();
            $table->integer('category_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('jobs_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('parent_category_id')->nullable();
            $table->timestamps();
        });

        Schema::create('jobs_opening_hours', function (Blueprint $table) {
            $table->id();
            $table->string('day_name')->index();
            $table->integer('job_id');
            $table->time('will_open_in');
            $table->time('will_close_in');
            $table->timestamps();

            $table->unique(['day_name', 'job_id']);
            $table->foreign('job_id')->references('id')->on('jobs')->cascadeOnDelete();
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('jobs_categories')->cascadeOnDelete();
        });

        DB::table('jobs_categories')->insert([
            ['name' => 'Saúde', 'parent_category_id' => null],
            ['name' => 'Primeiros Socorros', 'parent_category_id' => 1],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('jobs_opening_hours');
        Schema::dropIfExists('jobs_categories');
        Schema::dropIfExists('jobs');
    }
};
