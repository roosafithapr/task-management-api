<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('active_logins', function (Blueprint $table) {
            $table->string('session_id')->nullable()->unique()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('active_logins', function (Blueprint $table) {
            $table->dropColumn('session_id'); // Remove the session_id column
        });
    }
};
