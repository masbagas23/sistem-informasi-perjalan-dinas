<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_loans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedInteger('application_id');
            $table->unsignedInteger('vehicle_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('total_day');
            $table->unsignedInteger('requested_by');
            $table->unsignedInteger('approved_by')->nullable();
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('[1=>Waiting,2=>Approved,3=>Cancel,4=>Reject]');
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
        Schema::dropIfExists('vehicle_loans');
    }
}
