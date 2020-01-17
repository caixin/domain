<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerifyPathToDomainGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain_group', function (Blueprint $table) {
            $table->string('verify_path')->default('')->comment('驗證路徑')->after('path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domain_group', function (Blueprint $table) {
            $table->dropColumn('verify_path');
        });
    }
}
