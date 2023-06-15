<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('total_nominal')->default(0);
            $table->unsignedBigInteger('down_payment')->default(0);
            $table->bigInteger('remaining_cost')->default(0);
            $table->unsignedTinyInteger('is_reimburse')->default(0);
            $table->unsignedTinyInteger('status')->default(1)->comment('[1=>waiting,2=>done');
            $table->unsignedTinyInteger('status_reimburse')->nullable()->comment('[1=>waiting,2=>done');;
            $table->text('reimburse_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
