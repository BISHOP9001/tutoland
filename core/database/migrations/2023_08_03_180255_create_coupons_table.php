<?php

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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->string('code', 40)->nullable();
            $table->text('description')->nullable();
            $table->decimal('discount_amount', 28, 8)->default(0);
            $table->tinyInteger('discount_type')->default(0);
            $table->decimal('minimum_amount', 28, 8)->default(0);
            $table->decimal('maximum_amount', 28, 8)->default(0);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('coupons');
    }
};
