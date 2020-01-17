<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortToDomainGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain_group', function (Blueprint $table) {
            $table->string('sort', 100)->default('')->comment('排序')->after('value4');
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
            $table->dropColumn('sort');
        });
    }
}
