<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTripApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_trip_applications', function (Blueprint $table) {
            $table->id();
            $table->string('code',9);
            $table->string('code_letter',16)->nullable();
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('job_category_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('total_day')->default(0);
            $table->text('description');
            $table->text('note')->nullable();
            $table->unsignedInteger('requested_by');
            $table->unsignedTinyInteger('status')->default(1)->comment('[1=>Waiting,2=>Approve,3=>Cancel,4=>Reject]');
            $table->unsignedTinyInteger('result')->nullable()->comment('[1=>on progress,2=>done,3=>pending,4=>reschedule]');
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
        Schema::dropIfExists('business_trip_applications');
    }
}
