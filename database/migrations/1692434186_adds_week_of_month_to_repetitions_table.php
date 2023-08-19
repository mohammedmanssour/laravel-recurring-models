<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('repetitions', function (Blueprint $table) {
            $table->integer('week_of_month')->nullable()->after('week');
        });
    }

    public function down()
    {
        Schema::table('repetitions', function (Blueprint $table) {
            $table->dropColumn('week_of_month');
        });
    }
};
