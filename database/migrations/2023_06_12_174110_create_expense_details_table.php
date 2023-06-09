<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_id');
            $table->unsignedBigInteger('cost_category_id');
            $table->unsignedBigInteger('nominal');
            $table->text('description')->nullable();
            $table->text('file_path');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('[1=>waiting,2=>valid,3=>reject]');
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
        Schema::dropIfExists('expense_details');
    }
}
