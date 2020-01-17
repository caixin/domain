<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'api_log';

        Schema::create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('url', 300)->default('')->comment('API網址');
            $table->string('route', 200)->default('')->comment('路由');
            $table->json('param')->comment('參數');
            $table->json('return_str')->comment('回傳參數');
            $table->float('exec_time', 7, 4)->default('0.0000')->comment('執行時間');
            $table->string('ip', 50)->default('')->comment('IP');
            $table->dateTime('created_at')->default('1970-01-01 00:00:00')->comment('建檔時間');
            $table->index('created_at');
            $table->index('route');
        });

        DB::statement("ALTER TABLE `$tableName` comment 'API日誌'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_log');
    }
}
